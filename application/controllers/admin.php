<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends CI_Controller {
	public function  __construct(){
		parent::__construct();
		$this->load->model('admin_model');
		$this->load->model('urun_model');
		$this->data['base_url'] = base_url();
		$this->data['bilgilendirme'] = "";
	}
	public function index(){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}	
		else{ 
			redirect('admin/dashboard', 'refresh');
		}
	}
	public function login(){
		$this->load->view('admin/login',$this->data);  
	}
	public function dashboard(){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$this->load->view('admin/header');
		$this->load->view('admin/dashboard',$this->data);
		$this->load->view('admin/footer');
	}
	public function cikis_yap(){
		$this->admin_model->cikis_yap();
		$this->load->view('admin/login');
	}
	
	public function giris(){
		$email = $this->input->post('email');
		$sifre = $this->input->post('sifre');
		if($this->admin_model->giris_kontrol($email,$sifre)){				
				$this->data['bilgilendirme'] = "Hoşgeldiniz sayın Admin.";	
				$session_bilgileri = array(
					'yetki' => 'admin',
					'giris' => TRUE
				);
				$this->session->set_userdata($session_bilgileri);
			$this->load->view('admin/header');
			$this->load->view('admin/dashboard',$this->data);
			$this->load->view('admin/footer');
			}
		else {
			echo "basarisiz";
		}
	}
		
	
	public function urunler_kategori(){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$this->data['kategoriler'] = $this->urun_model->kategori_ile_urun_sayisi();
		$this->load->view('admin/header');
		$this->load->view('admin/urunler_with_kategori',$this->data);
		$this->load->view('admin/footer');

	}
	public function kategori_urunleri($kategori_id){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$this->data['urunler'] = $this->urun_model->kategori_urunleri($kategori_id);	
		$this->load->view('admin/header');
		$this->load->view('admin/kategori_urunleri',$this->data);
		$this->load->view('admin/footer');


}
    public function urun_ekle(){
		if(!$this->admin_model->admin_session_kontrol()){
				 redirect('admin/login', 'refresh');
		}	
	
		$this->load->model('urun_model');
		$this->data['kategoriler'] = $this->urun_model->get_kategoriler();
		$this->load->view('admin/header');
		$this->load->view('admin/urun_ekle',$this->data);
		$this->load->view('admin/footer');
    }
    public function kategori_kaydet(){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$kategori = $this->input->post('kategori');
		if($this->urun_model->kategori_ekle($kategori)){
			$this->data['bilgilendirme'] = "Kategori Ekleme Basarili";	
		}
		else {
			$this->data['bilgilendirme'] = "Sistemde sorun";	
		}
		$this->load->view('admin/header');
		$this->load->view('admin/kategori_ekle',$this->data);
		$this->load->view('admin/footer');
    }
    public function kategori_ekle(){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$this->load->view('admin/header');
		$this->load->view('admin/kategori_ekle',$this->data);
		$this->load->view('admin/footer');
	}
	public function urun_guncelle(){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$id = $this->input->post('id');
		$isim = $this->input->post('isim');
		$stok = $this->input->post('stok');
		$fiyat = $this->input->post('fiyat');
		$indirimsiz_fiyati = $this->input->post('indirimsiz_fiyati');
		$detay = $this->input->post('detay');
		$kategori_id = $this->input->post('kategori_id');
		if($this->urun_model->urun_guncelle($id,$isim,$stok,$indirimsiz_fiyati,$fiyat,$detay,$kategori_id)){
			$this->data['bilgilendirme'] = "Urun Guncelleme Basarili";		
		}
		else{
			$this->data['bilgilendirme'] = "Hata";		
		}
		$this->kategori_urunleri($kategori_id);
	}
    public function urun_kaydet(){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$this->load->library('SimpleImage');
		$this->data['kategoriler'] = $this->urun_model->get_kategoriler();
		$isim = $this->input->post('isim');
		$stok = $this->input->post('stok');
		$fiyat = $this->input->post('fiyat');
		$indirimsiz_fiyati = $this->input->post('indirimsiz_fiyati');
		$kategori_id = $this->input->post('kategori_id');
		$detay = $this->input->post('detay');
		$this->urun_model->urun_ekle($isim,$stok,$indirimsiz_fiyati,$fiyat,$kategori_id,$detay);	
		$id = $this->urun_model->get_id($isim,$stok,$fiyat,$kategori_id,$detay);			
		$uzanti=array('image/jpeg','image/jpg','image/png','image/x-png','image/gif');
		$dizin="images/urunler/orjinal";
		for($i=1;$i<10;$i++){
			if($_FILES['resim'.$i]['name']!= ""){
				$dosya = $_FILES['resim'.$i]['name']; 
				$adi = "su-med_".rand(0,1500).substr($dosya,-4);    	
				if(in_array(strtolower($_FILES['resim'.$i]['type']),$uzanti)){
					umask(0);
					move_uploaded_file($_FILES['resim'.$i]['tmp_name'],"./$dizin/$adi");
					$this->urun_model->resim_ekle($id,$adi);
					$image = new SimpleImage();
					$image->load(base_url().'images/urunler/orjinal/'.$adi);
					$image->resize(150,150);
					 $image->save('images/urunler/150x150/'.$adi);
					$this->data['bilgilendirme'] = "Urun Kaydetme Başarılı.";			}
				else{
					$this->data['bilgilendirme'] = "Sistemde Sorun.";
				}
			}
    		 }
		$this->load->view('admin/header');
		$this->load->view('admin/urun_ekle',$this->data);
		$this->load->view('admin/footer');	
	
	}
    public function urun_gor(){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$this->load->model('urun_model');
		$urunler = $this->urun_model->get_news(30);
    }
    public function kategori_sil_uyari($id){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$this->load->model('urun_model');
		$this->data['silinecekler'] = $this->urun_model->kategori_sil_uyari($id);
		$this->data['kategori_id'] = $id;
		$this->load->view('admin/header');
		$this->load->view('admin/kategori_sil_uyari',$this->data);
		$this->load->view('admin/footer');
    }
	public function kategori_sil(){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$id = $this->input->get('id');
		if($this->urun_model->kategori_sil($id)){
			$this->data['bilgilendirme'] = "Kategori Silme Başarılı";
			$this->kategori_duzenle_main();
		}
		else{
			$this->data['bilgilendirme'] = "Sistemde Sorun.";
			$this->kategori_duzenle_main();
		}
	}	
	public function urun_duzenle($id){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$this->data['kategoriler']=$this->urun_model->get_kategoriler();
		$this->data['urun'] = $this->urun_model->get_urun($id);
		$this->load->view('admin/header');
		$this->load->view('admin/urun_duzenle',$this->data);
		$this->load->view('admin/footer');
	}
	
      public function kategori_duzenle_main(){
		if(!$this->admin_model->admin_session_kontrol()){
				 redirect('admin/login', 'refresh');
			}
		$this->data['kategoriler'] = $this->urun_model->kategori_ile_urun_sayisi();		
		$this->load->view('admin/header');
		$this->load->view('admin/kategori_duzenle_main',$this->data);
		$this->load->view('admin/footer');
	
	}
	public function urun_sil($id,$kategori_id){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		if($this->urun_model->urun_sil($id)){
			$this->data['bilgilendirme'] = "Urun Silme Basarili";
			$this->kategori_urunleri($kategori_id);
	
		}
		else{
			$this->data['bilgilendirme'] = "Sistemde Sorun";
			$this->kategori_urunleri($kategori_id);	
		}
	}
	public function manset_resmi_kaydet(){
		$this->load->library('SimpleImage');
		$uzanti=array('image/jpeg','image/jpg','image/png','image/x-png','image/gif');
		$dizin="images/manset";
		umask(0);
		if($_FILES['manset']['name'] != ""){
				$dosya = $_FILES['manset']['name']; 
				$adi = "su-med_".rand(0,1500000).substr($dosya,-4);    	
				if(in_array(strtolower($_FILES['manset']['type']),$uzanti)){
					move_uploaded_file($_FILES['manset']['tmp_name'],"./".$dizin."/".$adi);
					$this->urun_model->manset_resmi_kaydet($adi);
					   $image = new SimpleImage();
					   $image->load(base_url().'images/manset/'.$adi);
					   $image->resize(960,430);
					   $image->save('images/manset/'.$adi);
					   $this->data['bilgilendirme'] = "Manset Ekleme Basarili";
				}
				else{
					 $this->data['bilgilendirme'] =  "Bu \"". $_FILES['resim'.$i]['type']."\" basarisiz";
				}
				$this->manset_resmi_ekle();	
		}
	}
	public function manset_resmi_ekle(){
		$this->load->view('admin/header');
		$this->load->view('admin/manset_resmi_ekle',$this->data);
		$this->load->view('admin/footer');
	}
	public function statik_sayfa_kategorisi_ekle(){
		$this->load->view('admin/header');
		$this->load->view('admin/statik_sayfa_kategorisi_ekle',$this->data);
		$this->load->view('admin/footer');
	}
	public function statik_sayfa_kategorisi_kaydet(){
		if(!$this->admin_model->admin_session_kontrol()){
			 redirect('admin/login', 'refresh');
		}
		$kategori = $this->input->post('kategori');
		if($this->statik_sayfa_model->statik_sayfa_kategorisi_ekle($kategori)){
			$this->data['bilgilendirme'] = "Kategori Ekleme Basarili";	
		}
		else {
			$this->data['bilgilendirme'] = "Sistemde sorun";	
		}
		$this->statik_sayfa_kategorisi_ekle();
	}
	public function gelen_alisverisler(){
		$this->data['alisverisler'] = $this->admin_model->gelen_alisverisler(NULL,'beklemede');
		$this->load->view('admin/header');
		$this->load->view('admin/gelen_alisverisler',$this->data);
		$this->load->view('admin/footer');
	}
	public function gelen_alisveris_detayli_goruntule($id){
		$this->data['alisverisler'] = $this->admin_model->gelen_alisverisler($id,'beklemede');
		$this->load->view('admin/header');
		$this->load->view('admin/gelen_alisveris_detayli_goruntule',$this->data);
		$this->load->view('admin/footer');
	}
	public function tamamlanan_alisverisler(){
		$this->data['alisverisler'] = $this->admin_model->gelen_alisverisler(NULL,'onaylandi');
		$this->load->view('admin/header');
		$this->load->view('admin/tamamlanan_alisverisler',$this->data);
		$this->load->view('admin/footer');
	}
	public function tamamlanan_alisveris_detayli_goruntule($id){
		$this->data['alisverisler'] = $this->admin_model->gelen_alisverisler($id,'onaylandi');
		$this->data['sepet_id'] = $id;
		$this->load->view('admin/header');
		$this->load->view('admin/tamamlanan_alisveris_detayli_goruntule',$this->data);
		$this->load->view('admin/footer');
	}
	public function sepet_onayla($id){
		$this->admin_model->sepet_onayla($id);
		$this->data['alisverisler'] = $this->admin_model->gelen_alisverisler(NULL,'beklemede');
		$this->load->view('admin/header');
		$this->load->view('admin/gelen_alisverisler',$this->data);
		$this->load->view('admin/footer');
	}
	public function kullanicilar(){
		$this->data['kullanicilar'] = $this->admin_model->kullanicilar();
		$this->load->view('admin/header');
		$this->load->view('admin/kullanicilar',$this->data);
		$this->load->view('admin/footer');
	}
	public function engelli_kullanicilar(){
		$this->data['kullanicilar'] = $this->admin_model->engelli_kullanicilar();
		$this->load->view('admin/header');
		$this->load->view('admin/engelli_kullanicilar',$this->data);
		$this->load->view('admin/footer');
	}
	public function kullanici_engelle($id){
		$this->admin_model->kullanici_engelle($id);
		$this->data['kullanicilar'] = $this->admin_model->kullanicilar();
		$this->load->view('admin/header');
		$this->load->view('admin/kullanicilar',$this->data);
		$this->load->view('admin/footer');
	}
	public function kullanici_engel_kaldir($id){
		$this->admin_model->kullanici_engel_kaldir($id);
		$this->data['kullanicilar'] = $this->admin_model->engelli_kullanicilar();
		$this->load->view('admin/header');
		$this->load->view('admin/engelli_kullanicilar',$this->data);
		$this->load->view('admin/footer');
	}
	public function okunmamis_mesajlar(){
		$this->data['okunmamis_mesajlar'] = $this->admin_model->okunmamis_mesajlar();
		$this->load->view('admin/header');
		$this->load->view('admin/okunmamis_mesajlar',$this->data);
		$this->load->view('admin/footer');
	}
	public function okunmus_mesajlar(){
		$this->data['okunmus_mesajlar'] = $this->admin_model->okunmus_mesajlar();
		$this->load->view('admin/header');
		$this->load->view('admin/okunmus_mesajlar',$this->data);
		$this->load->view('admin/footer');
	}
	public function okunmamis_mesaj_oku($id){
		$this->admin_model->mesaj_okundu_yap($id);
		$this->data['mesaj'] = $this->admin_model->mesaj_oku($id);
		$this->load->view('admin/header');
		$this->load->view('admin/mesaj_oku',$this->data);
		$this->load->view('admin/footer');
	}
	public function okunmus_mesaj_oku($id){
		$this->data['mesaj'] = $this->admin_model->mesaj_oku($id);
		$this->load->view('admin/header');
		$this->load->view('admin/mesaj_oku',$this->data);
		$this->load->view('admin/footer');
	}
	public function mesaj_sil($id){
		$this->data['mesaj'] = $this->admin_model->mesaj_sil($id);
		$this->load->view('admin/header');
		$this->load->view('admin/dashboard',$this->data);
		$this->load->view('admin/footer');
	}
	public function sepet_sil($id){
		$this->admin_model->sepet_sil($id);
		$this->load->view('admin/header');
		$this->load->view('admin/dashboard',$this->data);
		$this->load->view('admin/footer');
	}
	public function alisveris_tamamlanmadi_yap($id){
		$this->admin_model->alisveris_tamamlanmadi_yap($id);
		$this->load->view('admin/header');
		$this->load->view('admin/dashboard',$this->data);
		$this->load->view('admin/footer');
	}
	public function mesaj_okunmadi_olarak_isaretle($id){
		$this->admin_model->mesaj_okunmadi_yap($id);
		$this->okunmus_mesajlar();
	
	}
	public function kategori_duzenle($id){
		$this->data['kategori_isim'] = $this->urun_model->get_kategori_isim($id);
		$this->data['kategori_id'] = $id;
		$this->load->view('admin/header');
		$this->load->view('admin/kategori_duzenle',$this->data);
		$this->load->view('admin/footer');
	}
	public function kategori_duzenleme_kaydet(){
		$id = $this->input->post('id');
		$isim = $this->input->post('kategori');
		$this->admin_model->kategori_duzenleme_kaydet($id,$isim);
		$this->kategori_duzenle_main();
	}
	public function tamamlanan_alisveris_durum_ekle(){
		$sepet_id = $this->input->post('sepet_id');
		$durum = $this->input->post('durum');
		$this->admin_model->tamamlanan_alisveris_durum_ekle($sepet_id,$durum);
		$this->tamamlanan_alisveris_detayli_goruntule($sepet_id);
	}
	
}	
?>
