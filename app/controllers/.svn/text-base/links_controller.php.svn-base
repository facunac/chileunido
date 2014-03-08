<?php
class LinksController extends AppController {
    var $name = "Links";

    function subir() {
        $this->escribirHeader("Links");

        if (!empty($this->data)) {

            if( $this->isTooBig( $this->data['File'])=='fail') {
                $this->set('errorArchivo', 'El tamaño del archivo es demasiado grande');
                return;
            }

            if( $this->isNull( $this->data['File'])=='fail') {
                $this->set('errorArchivo', 'Falta agregar un archivo');
                return;
            }
            else {
                $this->set('errorArchivo', '');
            }

            $this->data["Link"]['descripcion']= $this->borrarCaracteresProblematicos($this->data["Link"]['descripcion']);

            $fileOK = $this->uploadFiles('storedFiles', $this->data['File'], $this->data["Link"]['nombre']);#

            if ($this->Link->save($this->data)) {
                $this->flash('Tu archivo fue guardado.','/links');
            }

        }
        else {
            $this->set('errorArchivo', '');
        }

    }

    public function borrarCaracteresProblematicos($text) {
        // removemos < y >
        $text = preg_replace('~[<>]+~', '', $text);
        
        // esto remueve caracteres extraños, pero está comentado porque por ahora no parecen provocar errores los caracteres extraños
        //$text = preg_replace('~[^-\w\s\.]+~', '', $text);

        if (empty($text)) {
            return 'nombreGenerico';
        }

        return $text;
    }

    public function slugify($text) {

        $text = preg_replace("[á]","a",$text);
        $text = preg_replace("[í]","i",$text);
        $text = preg_replace("[é]","e",$text);
        $text = preg_replace("[ó]","o",$text);
        $text = preg_replace("[ú]","u",$text);
        $text = preg_replace("[ñ]","n",$text);

        //$text = preg_replace('/[\w\w]\./', '_.', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w\.]+~', '_', $text);

        if (empty($text)) {
            return 'nombreGenerico';
        }

        return $text;
    }

    function isTooBig($files) {
        foreach($files as $file) {
            if($file['size']>32000000)
                return 'fail';
        }
        return 'win';
    }

    function isNull($files) {
        foreach($files as $file) {
            if ($file['name']==null)
                return 'fail';
        }

        return 'win';
    }

    function index() {

        $this->escribirHeader("Bajar");

        $archivos = $this->listarArchivos();

        $links = $this->Link->findAll();
        $linksAuxiliar = $this->Link->findAll();

        foreach ($links as $key => $link) {
            if($archivos[$link['Link']['nombre']]==null) {

                $this->Link->del($link['Link']['id']);

                unset($linksAuxiliar[$key]);
            }
        }

        $this->set('links', $linksAuxiliar);
    }

    function bajar() {
        $this->redirect('/links');
        $this->redirect('/links');
    }

    function borrar($id=null) {


        $this->escribirHeader("Borrar");

        if($id==null) {
            $archivos = $this->listarArchivos();

            $links = $this->Link->findAll();

            foreach ($links as $link) {
                if($archivos[$link['Link']['nombre']]==null) {

                    $this->Link->del($link['Link']['id']);
                }


            }

            $this->set('links', $this->Link->findAll());
        }else {


            $hola = $this->Link->findById($id);

            unlink(WWW_ROOT.'storedFiles/'.$hola['Link']['nombre']);

            $this->Link->del($id);
            $this->flash('El link con id: '.$id.' fue borrado.', '/links');
        }
    }


    function uploadFiles($folder, $formdata, $temporaryFileName) {

    // se setea donde se va a guardar el archivo
        $folder_url = WWW_ROOT.$folder;
        $rel_url = $folder;
        $success;

        // loop por todos los files de formdata
        foreach($formdata as $file) {

        // reemplazamos el nombre y lo cambiamos por underscores

            if($temporaryFileName=="") {
                $filename = $file['name'];
                $filename = $this->slugify($filename);
            }
            else {
                $pieces = explode(".", $file['name']);

                $temporaryFileName = $this->slugify($temporaryFileName);

                $filename = $temporaryFileName.'.'.$pieces[count($pieces)-1];
            }

            if($this->Link->findByNombre($filename)==null) {
            // subir el archivo
                $full_url = $folder_url.'/'.$filename;
                $url = $rel_url.'/'.$filename;
                // upload the file
                $success = move_uploaded_file($file['tmp_name'], $url);
            } else {
            // si el nombre de archivo ya existe lo volvemos a
                ini_set('date.timezone', 'Europe/London');
                $now = date('Y-m-d');
                $filename = $now.$filename;
                $full_url = $folder_url.'/'.$filename;
                $url = $rel_url.'/'.$filename;
                $success = move_uploaded_file($file['tmp_name'], $url);
            }

            $this->data["Link"]['nombre']=$filename;
        }
        return $success;
    }


    function listarArchivos() {
        $directorio = opendir("storedFiles/");
        $i = 1;
        while (false !== ($archivo = readdir($directorio))) {
            if($archivo != '..' && $archivo != '.') {
                $listalinksarchivos[$archivo] = $archivo;
                $i++;
            }
        }
        closedir($directorio);
        return $listalinksarchivos;
    }


}
?>
