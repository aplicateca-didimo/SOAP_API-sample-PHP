<?php
if (isset($_POST['txtUser'], $_POST['txtPassword']))
{
    $username=$_POST['txtUser'];
    $password=$_POST['txtPassword'];
    $sender=$_POST['txtSender'];
    $text=$_POST['txtText'];
    $mobile=$_POST['txtMobile'];
    $scheduleDate=$_POST['txtDate'];
    $id=$_POST['txtId'];
    $name=$_POST['txtName'];

    $result = CreateMessages($username, $password, $id, $name, $sender, $text, $mobile, $scheduleDate);

    $xml = simplexml_load_string($result);
    
    foreach($xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children('http://aplicateca.didimo.es/')->CreateMessageResponse->CreateMessageResult as $item)
    {
        $errorCode = (string)$item->ErrorCode;
        $errorDescription = (string)$item->ErrorDescription;
        $idResult = (string)$item->Result->Id;
        $totalMessages = (string)$item->Result->TotalMessages;
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>DIDIMO - Cliente API PHP</title>
    </head>
    <body style="width: 750px;">
        <div style="width: 100%;">
            <table>
                <tr>
                    <td style="padding: 10px;"><a href="index.php" style=" color: #000; text-decoration: none;">Home</a></td>
                    <td style="padding: 10px;"><a href="ping.php" style=" color: #000; text-decoration: none;">Ping</a></td>
                    <td style="padding: 10px;"><a href="getcredits.php" style=" color: #000; text-decoration: none;">GetCredits</a></td>
                    <td style="padding: 10px; border: solid 1px #f00;"><a href="createmessage.php" style=" color: #f00; text-decoration: none;">CreateMessage</a></td>
                </tr>
            </table>
        </div>
        <hr/>
        <div style="width: 100%;">
            <h1>CreateMessage</h1>
        </div>
        <div style="width: 100%;">
            <form method="post" action="createmessage.php">
                <table border="0" style="width: 100%;">
                    <tr>
                        <td style="padding: 5px;">
                            <span style="color: #f00;">*</span><span>Usuario: </span><input type="text" name="txtUser" value="<?php printr($username); ?>"/>
                        </td>
                        <td style="padding: 5px;">
                            <span style="color: #f00;">*</span><span>Contraseña: </span><input type="password" name="txtPassword" value="<?php printr($password); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px;">
                            <span style="color: #f00;">*</span><span>Remitente: </span><input type="text" name="txtSender" value="<?php printr($sender); ?>"/>
                        </td>
                        <td style="padding: 5px;">
                            <span style="color: #f00;">*</span><span>Texto: </span><input type="text" name="txtText" value="<?php printr($text); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px;">
                            <span style="color: #f00;">*</span><span>Móvil: </span><input type="text" name="txtMobile" value="<?php printr($mobile); ?>"/>
                        </td>
                        <td style="padding: 5px;">
                            <span>Fecha Envío: </span><input type="text" name="txtDate" value="<?php printr($scheduleDate); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px;">
                            <span>ID: </span><input type="text" name="txtId" value="<?php printr($id); ?>"/>
                        </td>
                        <td style="padding: 5px;">
                            <span>Nombre Envío: </span><input type="text" name="txtName" value="<?php printr($name); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right; padding: 5px;" colspan="2">
                            <input type="submit" name="btnPing" value="enviar">
                        </td>
                    </tr>
                </table>
                <p style="color: #f00;">(*) Datos Obligatorios</p>
            </form>
        </div>
        <hr/>
        <div style="width: 100%;">
            <h2>Resultado</h2>
            <table style="width: 100%;">
                <tr>
                    <td>
                        <span style="font-weight: bold;">ErrorCode: </span><span style="color: #f00;"><?php printr($errorCode); ?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">ErrorDescription: </span><span style="color: #f00;"><?php printr($errorDescription); ?></span>
                        
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">Id: </span><span style="color: #f00;"><?php printr($idResult); ?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">TotalMessages: </span><span style="color: #f00;"><?php printr($totalMessages); ?></span>
                    </td>
                </tr>
            </table>
            <hr/>
            <h2>Respuesta XML SOAP</h2>
            <table style="width: 100%;">
                <tr>
                    <td>
                        <textarea name="txtResponse" style="width: 100%; height: 300px;"><?php printr( $result); ?></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
<?php

function CreateMessages($username, $password, $id, $name, $sender, $text, $mobile, $scheduleDate)
{
    # Method
    $method_invoke_soap_action = "SOAPAction: http://aplicateca.didimo.es/CreateMessage";
    $service_url = "https://aplicateca.didimo.es/custws/service.asmx";

    # Xml
    $method_invoke_soap_xml = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <CreateMessage xmlns="http://aplicateca.didimo.es/">
          <login>'.$username.'</login>
          <password>'.$password.'</password>
          <id>'.$id.'</id>
          <name>'.$name.'</name>
          <sender>'.$sender.'</sender>
          <text>'.$text.'</text>
          <mobile>'.$mobile.'</mobile>
          <scheduleDate>'.$scheduleDate.'</scheduleDate>
        </CreateMessage>
      </soap:Body>
    </soap:Envelope>';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $service_url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8", $method_invoke_soap_action));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $method_invoke_soap_xml);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    $info = curl_getinfo($ch);
    if (0 == curl_errno($ch))
    {
        $result = curl_exec($ch);
    }
    else
    {
        $result = curl_error($ch);
    }

    curl_close($ch);

    return $result ;
}

function printr($a)
{
    ob_start();
    print_r($a);
    $t=ob_get_contents();
    ob_end_clean();
    echo nl2br(str_replace(" ","&nbsp;",$t));
}

?>