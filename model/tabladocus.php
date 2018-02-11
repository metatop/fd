<?php



/**
 * Relaciona un documentos almacenado con una averia, marca, chasis, etc...
 *
 * 
 */
class tabladocus extends fs_model
{
   public $id;
   public $ruta;
   public $nombre;
   public $descripcion;
   public $fecha;
   public $hora;
   public $tamano;
   public $usuario;
   public $idaparato;
   public $idaveria;
   public $chasis;  //id chasis
   public $marca;  // id marca
   
   
   private $file_exist;
   
   public function __construct($d = FALSE)
   {
      parent::__construct('tabladocus');
      if($d)
      {
         $this->id = $this->intval($d['id']);
         $this->ruta = $d['ruta'];
         $this->nombre = $d['nombre'];
         $this->descripcion = $d['descripcion'];
         $this->fecha = date('d-m-Y', strtotime($d['fecha']));
         $this->hora = date('h:i:s', strtotime($d['hora']));
         $this->tamano = intval($d['tamano']);
         $this->usuario = $d['usuario'];
         $this->idfactura = $this->intval($d['idaparato']);
         $this->idalbaran = $this->intval($d['idaveria']);
         $this->idpedido = $this->intval($d['chasis']);
         $this->idpresupuesto = $this->intval($d['marca']);
         
      }
      else
      {
         $this->id = NULL;
         $this->ruta = NULL;
         $this->nombre = NULL;
         $this->descripcion = NULL;
         $this->fecha = date('d-m-Y');
         $this->hora = date('h:i:s');
         $this->tamano = 0;
         $this->usuario = NULL;
         $this->idfaparato = NULL;
         $this->idaveria = NULL;
         $this->chasis = NULL;
         $this->marca = NULL;
         
      }
   }
   
   protected function install()
   {
      return '';
   }
   
   public function file_exists()
   {
      if( !isset($this->file_exist) )
      {
         $this->file_exist = file_exists($this->ruta);
      }
      
      return $this->file_exist;
   }
   
   public function tamano()
   {
      $bytes = $this->tamano;
      $decimals = 2;
      $sz = 'BKMGTP';
      $factor = floor((strlen($bytes) - 1) / 3);
      return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor];
   }
   
   public function get($id)
   {
      $data = $this->db->select("SELECT * FROM tabladocus WHERE id = ".$this->var2str($id).";");
      if($data)
      {
         return new tabladocus($data[0]);
      }
      else
         return FALSE;
   }
   
   public function exists()
   {
      if( is_null($this->id) )
      {
         return FALSE;
      }
      else
         return $this->db->select("SELECT * FROM tabladocus WHERE id = ".$this->var2str($this->id).";");
   }
   
   
   public function save()
   {
      if( $this->exists() )
      {
         $sql = "UPDATE tabladocus SET ruta = ".$this->var2str($this->ruta)
                 .", nombre = ".$this->var2str($this->nombre)
                 .", descripcion = ".$this->var2str($this->descripcion)
                 .", fecha = ".$this->var2str($this->fecha)
                 .", hora = ".$this->var2str($this->hora)
                 .", tamano = ".$this->var2str($this->tamano)
                 .", usuario = ".$this->var2str($this->usuario)
                 .", idaparato = ".$this->var2str($this->idaparato)
                 .", idaveria = ".$this->var2str($this->idaveria)
                 .", chasis = ".$this->var2str($this->chasis)
                 .", marca = ".$this->var2str($this->marca)
                 ." WHERE id = ".$this->var2str($this->id).";";
         
         return $this->db->exec($sql);
      }
      else
      {
         $sql = "INSERT INTO tabladocus (ruta,nombre,descripcion,fecha,hora,tamano,usuario,"
                 . "idaparato,idaveria,chasis,marca) VALUES (".$this->var2str($this->ruta)
                 . ",".$this->var2str($this->nombre)
                 . ",".$this->var2str($this->descripcion)
                 . ",".$this->var2str($this->fecha)
                 . ",".$this->var2str($this->hora)
                 . ",".$this->var2str($this->tamano)
                 . ",".$this->var2str($this->usuario)
                 . ",".$this->var2str($this->idaparato)
                 . ",".$this->var2str($this->idaveria)
                 . ",".$this->var2str($this->chasis)
                 . ",".$this->var2str($this->marca).");";
         
         if( $this->db->exec($sql) )
         {
            $this->id = $this->db->lastval();
            return TRUE;
         }
         else
            return FALSE;
      }
   }
   
   public function delete()
   {
      return $this->db->exec("DELETE FROM tabladocus WHERE id = ".$this->var2str($this->id).";");
   }
   
   /**
    * Devuelve todos los documentos relacionados.
    * @param type $tipo
    * @param type $id
    * @return \tabladocus
    */
   public function all_from($tipo, $id)
   {
      $lista = array();
      $sql = "SELECT * FROM tabladocus WHERE ".$tipo." = ".$this->var2str($id).";";
      
      $data = $this->db->select($sql);
      if($data)
      {
         foreach($data as $d)
         {
            $lista[] = new tabladocus($d);
         }
      }
      
      return $lista;
   }
}
