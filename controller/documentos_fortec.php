<?php

/*
 * This file is part of FacturaSctipts
 * Copyright (C) 2015-2016  Carlos Garcia Gomez  neorazorx@gmail.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_model('tabladocus.php');
require_model('tablaaparatos.php');
require_model('tablaaverias.php');
require_model('tablachasis.php');
require_model('tablamarcas.php');



class documentos_fortec extends fs_controller
{
   public $documentos;
   
   public function __construct()
   {
      parent::__construct(__CLASS__, 'Documentos', 'Averias', FALSE, FALSE);
   }
   
   protected function private_core()
   {
      $this->share_extension();
      
      $this->check_documentos();
      $this->documentos = array();
      
      if( isset($_GET['folder']) AND isset($_GET['id']) )
      {
         if( isset($_POST['upload']) )
         {
            $this->upload_documento();
         }
         else if( isset($_GET['delete']) )
         {
            $this->delete_documento();
         }
         
         $this->documentos = $this->get_documentos();
      }
   }
   
   private function upload_documento()
   {
      if( is_uploaded_file($_FILES['fdocumento']['tmp_name']) )
      {
         $nuevon = $this->random_string(6).'_'.$_FILES['fdocumento']['name'];
         
         if ($_FILES['fdocumento']['size'] > 35024000)
            {
                $this->new_error_msg('El tamaño del archivo es superior a 35Mb y no se ha guardado');
                //$seguir= "NO";
            }
         else
         if( copy($_FILES['fdocumento']['tmp_name'], 'documentos/'.$nuevon) )
         {
            $doc = new tabladocus();
            $doc->ruta = 'documentos/'.$nuevon;
            $doc->nombre = $_FILES['fdocumento']['name'];
            $doc->descripcion = $_POST['descripcion'];
            $doc->tamano = filesize(getcwd().'/'.$doc->ruta);
            $doc->usuario = $this->user->nick;
            
            if($_GET['folder'] == 'docusaveria')
            {
               $doc->idaveria = $_GET['id'];
            }
            else if($_GET['folder'] == 'docusaparato')
            {
               $doc->idaparato = $_GET['id'];
            }
            else if($_GET['folder'] == 'docusmarca')
            {
               $doc->marca = $_GET['id'];
            }
            else if($_GET['folder'] == 'presupuestoscli')
            {
               $doc->idpresupuesto = $_GET['id'];
            }
            else if($_GET['folder'] == 'facturasprov')
            {
               $doc->idfacturaprov = $_GET['id'];
            }
            else if($_GET['folder'] == 'albaranesprov')
            {
               $doc->idalbaranprov = $_GET['id'];
            }
            else if($_GET['folder'] == 'pedidosprov')
            {
               $doc->idpedidoprov = $_GET['id'];
            }
            
            if( $doc->save() )
            {
               $this->new_message('Documento añadido correctamente.');
               
            }
            else
            {
               $this->new_error_msg('Error al asignar el archivo.');
               @unlink($doc->ruta);
            }
         }
         else
         {
            $this->new_error_msg('Error al mover el archivo.');
         }
      }
   }
   
   private function delete_documento()
   {
      $doc0 = new tabladocus();
      
      $documento = $doc0->get($_GET['delete']);
      if($documento)
      {
         if( $documento->delete() )
         {
            $this->new_message('Documento eliminado correctamente.');
            @unlink($documento->ruta);
         }
         else
         {
            $this->new_error_msg('Error al eliminar el documento.');
         }
      }
      else
      {
         $this->new_error_msg('Documento no encontrado.');
      }
   }
   
   private function share_extension()
   {
      $extensiones = array(
          array(
              'name' => 'documentos_editar_averia',
              'page_from' => __CLASS__,
              'page_to' => 'editar_averia',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span> <span class="hidden-xs">&nbsp; Gestionar los Archivos Adjuntos</span>',
              'params' => '&folder=docusaveria' // Mete el valor del folder a la url al hacer clic en el tab
          ),
          array(
              'name' => 'documentos_detalle_averia',
              'page_from' => __CLASS__,
              'page_to' => 'detalle_averia',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span> <span class="hidden-xs">&nbsp; Documentos</span>',
              'params' => '&folder=docusaveria'
          ),
          array(
              'name' => 'nuevodocaveria',
              'page_from' => __CLASS__,
              'page_to' => 'nuevo_doc_averia',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span> <span class="hidden-xs">&nbsp; Documentos</span>',
              'params' => '&folder=docusaveria'
          ),
          array(
              'name' => 'detalleaparato',
              'page_from' => __CLASS__,
              'page_to' => 'detalle_aparato',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span> <span class="hidden-xs">&nbsp; Documentos</span>',
              'params' => '&folder=docusaparato'
          ),       
          array(
              'name' => 'detallemarca',
              'page_from' => __CLASS__,
              'page_to' => 'detalle_marca',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span> <span class="hidden-xs">&nbsp; Documentos</span>',
              'params' => '&folder=docusmarca'
          ),     
          array(
              'name' => 'editarmarca',
              'page_from' => __CLASS__,
              'page_to' => 'editar_marca',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span> <span class="hidden-xs">&nbsp; Documentos</span>',
              'params' => '&folder=docusmarca'
          ),     
      );
      foreach($extensiones as $ext)
      {
         $fsext = new fs_extension($ext);
         $fsext->save();
      }
   }
   
   public function url()
   {
      if( isset($_GET['folder']) AND isset($_GET['id']) )
      {
         return 'index.php?page='.__CLASS__.'&folder='.$_GET['folder'].'&id='.$_GET['id'];
      }
      else
         return parent::url();
   }
   
   public function esImagen($path) //Para comprobar si el archivo guardado es una imagen
   {
        $imageSizeArray = getimagesize($path);
        $imageTypeArray = $imageSizeArray[2];
        return (bool)(in_array($imageTypeArray , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)));
   }
    
    
   private function get_documentos()
   {
      $doc = new tabladocus();
      if($_GET['folder'] == 'docusaveria')
      {
         /*comprobamos los albaranes relacionados con esta factura
         $alba = new albaran_cliente();
         foreach($alba->all_from_factura($_GET['id']) as $alb)
         {
            foreach( $doc->all_from('idalbaran', $alb->idalbaran) as $d )
            {
               $d->idfactura = $_GET['id'];
               $d->save();
            }
         } */
         
         return $doc->all_from('idaveria', $_GET['id']);
      }
      else if($_GET['folder'] == 'docusaparato')
      {
         /*if( class_exists('pedido_cliente') )
         {
            /// comprobamos los pedidos relacionados con este albarán
            $pedi = new pedido_cliente();
            foreach($pedi->all_from_albaran($_GET['id']) as $ped)
            {
               foreach( $doc->all_from('idpedido', $ped->idpedido) as $d )
               {
                  $d->idalbaran = $_GET['id'];
                  $d->save();
               }
            }
         }*/
         
         return $doc->all_from('idaparato', $_GET['id']);
      }
      else if($_GET['folder'] == 'docusmarca')
      {
         /* comprobamos los presupuestos relacionados con este pedido
         $presu = new presupuesto_cliente();
         foreach($presu->all_from_pedido($_GET['id']) as $pre)
         {
            foreach( $doc->all_from('idpresupuesto', $pre->idpresupuesto) as $d )
            {
               $d->idpedido = $_GET['id'];
               $d->save();
            }
         }*/
         
         return $doc->all_from('marca', $_GET['id']);
      }
      else if($_GET['folder'] == 'presupuestoscli')
      {
         return $doc->all_from('idpresupuesto', $_GET['id']);
      }
      else if($_GET['folder'] == 'facturasprov')
      {
         /// comprobamos los albaranes relacionados con esta factura
         $alba = new albaran_proveedor();
         foreach($alba->all_from_factura($_GET['id']) as $alb)
         {
            foreach( $doc->all_from('idalbaranprov', $alb->idalbaran) as $d )
            {
               $d->idfacturaprov = $_GET['id'];
               $d->save();
            }
         }
         
         return $doc->all_from('idfacturaprov', $_GET['id']);
      }
      else if($_GET['folder'] == 'albaranesprov')
      {
         if( class_exists('pedido_proveedor') )
         {
            /// comprobamos los pedidos relacionados con este albarán
            $pedi = new pedido_proveedor();
            foreach($pedi->all_from_albaran($_GET['id']) as $ped)
            {
               foreach( $doc->all_from('idpedidoprov', $ped->idpedido) as $d )
               {
                  $d->idalbaranprov = $_GET['id'];
                  $d->save();
               }
            }
         }
         
         return $doc->all_from('idalbaranprov', $_GET['id']);
      }
      else if($_GET['folder'] == 'pedidosprov')
      {
         return $doc->all_from('idpedidoprov', $_GET['id']);
      }
      else
      {
         return array();
      }
   }
   
   private function check_documentos()
   {
      if( !file_exists('documentos') )
      {
         mkdir('documentos');
      }
      
      if( isset($_GET['folder']) AND isset($_GET['id']) )
      {
         /// comprobamos la antigua rura
         $folder = 'tmp/'.FS_TMP_NAME.'docus/'.$_GET['folder'].'/'.$_GET['id'];
         if( file_exists($folder) )
         {
            foreach( scandir($folder) as $f )
            {
               if($f != '.' AND $f != '..')
               {
                  /// movemos a la nueva ruta
                  $nuevon = $this->random_string(6).'_'.(string)$f;
                  if( rename($folder.'/'.$f, 'documentos/'.$nuevon) )
                  {
                     $doc = new tabladocus();
                     $doc->ruta = 'documentos/'.$nuevon;
                     $doc->nombre = (string)$f;
                     $doc->tamano = filesize(getcwd().'/'.$doc->ruta);
                     $doc->usuario = $this->user->nick;
                     
                     if($_GET['folder'] == 'docusaveria')
                     {
                        $doc->idaveria = $_GET['id'];
                     }
                     else if($_GET['folder'] == 'docusaparato')
                     {
                        $doc->idaparato = $_GET['id'];
                     }
                     
                     if( !$doc->save() )
                     {
                        $this->new_error_msg('Error al mover el archivo.');
                     }
                  }
                  else
                  {
                     $this->new_error_msg('Error al mover el archivo a la nueva ruta.');
                  }
               }
            }
         }
      }
   }
}
