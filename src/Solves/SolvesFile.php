<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 18/07/2019
 */ 
namespace Solves;


class SolvesFile {
    public static function saveThumb($urlFoto, $urlDestinoFoto, $extensao, $contentType, $nomeArquivo, $width, $height) {
        $maxWidth = ($width ? $width : 150);
        $maxHeight = ($height ? $height : 150);
        # Carrega a imagem
        $img = null;
        $isJpeg = ($extensao == 'jpg' || $extensao == 'jpeg');
        $isPng = (!$isJpeg && ($extensao == 'png'));
        $isGif = (!$isJpeg && !$isPng && ($extensao == 'gif'));
        if ($isJpeg) {
            $img = @imagecreatefromjpeg($urlFoto);
        } else if ($isPng) {
            $img = @imagecreatefrompng($urlFoto);
        } else if ($isGif) {
            $img = @imagecreatefromgif($urlFoto);
        }
        // Se a imagem foi carregada com sucesso, testa o tamanho da mesma
        if ($img) {
            // Pega o tamanho da imagem e proporção de resize
            $width = imagesx($img);
            $height = imagesy($img);
            $scale = min($maxWidth / $width, $maxHeight / $height);

            // Se a imagem é maior que o permitido, encolhe ela!
            if ($scale < 1) {
                $new_width = floor($scale * $width);
                $new_height = floor($scale * $height);

                // Cria uma imagem temporária
                $tmp_img = imagecreatetruecolor($new_width, $new_height);
                if ($isGif || $isPng) {
                    imagecolortransparent($tmp_img, imagecolorallocatealpha($tmp_img, 0, 0, 0, 127));
                    imagealphablending($tmp_img, false);
                    imagesavealpha($tmp_img, true);
                }

                // Copia e redimensiona a imagem velha na nova
                imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagedestroy($img);
                $img = $tmp_img;
            }
        } else {
            $img = imagecreate($maxWidth, $maxHeight);

            imagecolorallocate($img, 204, 204, 204);

            $c = imagecolorallocate($img, 153, 153, 153);
            $c1 = imagecolorallocate($img, 0, 0, 0);

            imageline($img, 0, 0, $maxWidth, $maxHeight, $c);
            imageline($img, $maxWidth, 0, 0, $maxHeight, $c);
            imagestring($img, 2, 12, 55, 'erro ao carregar imagem', $c1);
        }
        if ($isJpeg) {
            imagejpeg($img, $urlDestinoFoto);
        } else if ($isPng) {
            imagepng($img, $urlDestinoFoto);
        } else if ($isGif) {
            imagegif($img, $urlDestinoFoto);
        }
        imagedestroy($img);
    }
    public static function normalizeImgUrl($string) {
        return (Solves::isNotBlank($string) ? (strpos($string,'data:')>0 ? Solves::substituiEspacos($string, "+")  : $string):"");
    }
    public static function getFileBlobUploaded($paramHttpFile, $paramFileName){
        if( isset($paramHttpFile[$paramFileName]) and !$paramHttpFile[$paramFileName]['error']  && $paramHttpFile[$paramFileName]['size'] > 0){
            $file = file_get_contents($paramHttpFile[$paramFileName]['tmp_name']);
            return ($file);
        }
        return null;
    }
}