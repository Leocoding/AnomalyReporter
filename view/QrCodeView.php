<?php


class QrCodeView{

    // Renvoie le code html contenant le squelette d'une etiquette de ressource.
    // Ce squelette sera modifié depuis un script js
    public static function getQRCodeAutoPrintPage(){
        $qrprint = "<div id='qrcode'>
                        <div>
                            <div>
                                <h2>Flashez moi</h2>
                                <p>Pour signaler un problème avec ce matériel</p>
                            </div>
                            <p id='url'></p>
                        </div>
                    </div>";
        return $qrprint;
    }
}
?>