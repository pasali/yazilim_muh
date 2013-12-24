<?php
class Pagination_model extends CI_Model {
	public function kategori_urun_toplam($kategori_id){
		$sql = "SELECT * FROM `urun` WHERE `kategori_id`='$kategori_id'";
		$urunler = $this->db->query($sql);
		if ($urunler){
		    return $urunler->num_rows();
		}
		return FALSE;
	}
	public function indirimli_urun_toplam(){
		$sql = "SELECT * FROM `urun` where `indirimsiz_fiyati`!=0";
		$urunler = $this->db->query($sql);
		if ($urunler){
		    return $urunler->num_rows();
		}
		return FALSE;
	}
	public function populer_urun_toplam(){
		$sql = "SELECT * FROM `urun`";
		$urunler = $this->db->query($sql);
		if ($urunler){
		    return $urunler->num_rows();
		}
		return FALSE;
	}
	public function get_resim($id){
		$sql = "SELECT isim FROM  `resimler` WHERE  `urun_id` = $id;";
		$query = $this->db->query( $sql);
		if( $query->num_rows() > 0 ){
		    return $query->result_array();
		}
		else {
			return FALSE;
		}
	}
	public function kategori_urunleri($kategori_id,$baslangic,$limit){
		$sql = "SELECT * FROM  `urun` WHERE `kategori_id`='$kategori_id' LIMIT $baslangic,$limit ";
		$query = $this->db->query( $sql);
		$urunler=array();
		if( $query->num_rows() > 0 ){
			foreach ( $query->result_array() as $urun){
		    		array_push($urunler,array($urun,$this->get_resim($urun['id'])));	
			}		 
			return $urunler;		
		}
		else {
			return FALSE;
		}	
	}
	public function populer_urunler($baslangic,$limit){
		$sql = "SELECT * FROM  `urun`  order by tiklanma desc LIMIT $baslangic,$limit ";
		$query = $this->db->query( $sql);
		$urunler=array();
		if( $query->num_rows() > 0 ){
			foreach ( $query->result_array() as $urun){
		    		array_push($urunler,array($urun,$this->get_resim($urun['id'])));	
			}		 
			return $urunler;		
		}
		else {
			return FALSE;
		}	

	}
public function indirimli_urunler($baslangic,$limit){
		$sql = "SELECT * FROM  `urun`  where `indirimsiz_fiyati`!=0 order by id desc LIMIT $baslangic,$limit ";
			$query = $this->db->query( $sql);
			$urunler=array();
			if( $query->num_rows() > 0 ){
				foreach ( $query->result_array() as $urun){
				  	array_push($urunler,array($urun,$this->get_resim($urun['id'])));	
				}		 
				return $urunler;		
			}
			else {
				return FALSE;
			}
	}
}
