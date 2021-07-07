<?php
/**
 * The Template for displaying the body section of the emails.
 *
 * This template can be overridden by copying it to yourtheme/wpum/emails/body-default.php
 *
 * HOWEVER, on occasion WPUM will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Determine the output of the content.
// If we're loading this file through the editor
// we'll show fake content so the user can edit it.
$output = '{email}';

if( isset( $data->preview ) && $data->preview === true ) {
	$output = '<div class="preview-content">' . wpum_get_email_field( $data->email_id, 'content' ) . '</div>';
}

// {email} is replaced by the content entered in the customizer.
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title></title>
    <style type="text/css">
        html {
        -webkit-text-size-adjust: none;
        -ms-text-size-adjust: none;
        }
        @media only screen and (min-device-width: 640px) {
        .table640 {
        width: 640px !important;
        }
        }
        @media only screen and (max-device-width: 640px), only screen and (max-width: 640px) {
        .table640 {
        width: 100% !important;
        }
        .mob_left {
        text-align: left !important;
        }
        .mob_soc {
        width: 50% !important;
        max-width: 50% !important;
        min-width: 50% !important;
        }
        .mob_menu {
        width: 50% !important;
        max-width: 50% !important;
        min-width: 50% !important;
        box-shadow: inset -1px -1px 0 0 rgba(255, 255, 255, 0.2);
        }
        .mob_card {
        width: 86% !important;
        max-width: 86% !important;
        min-width: 86% !important;
        }
        .mob_card1 {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 100% !important;
        }
        .mob_title1 {
        font-size: 36px !important;
        line-height: 40px !important;
        }
        .mob_title2 {
        font-size: 26px !important;
        line-height: 33px !important;
        }
        .top_pad {
        height: 15px !important;
        max-height: 15px !important;
        min-height: 15px !important;
        }
        .mob_pad {
        width: 15px !important;
        max-width: 15px !important;
        min-width: 15px !important;
        }
        .top_pad2 {
        height: 40px !important;
        max-height: 40px !important;
        min-height: 40px !important;
        }
        .mob_txt {
        font-size: 16px !important;
        line-height: 26px !important;
        }
        }
        @media only screen and (max-device-width: 550px), only screen and (max-width: 550px) {
        .mod_div {
        display: block !important;
        }
        .mob_btn {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 100% !important;
        }
        .table640 {
        width: 640px;
        }
        }
        .mob_title1 {
        font-size: 36px !important;
        line-height: 40px !important;
        }
        .mob_txt {
        font-size: 16px !important;
        line-height: 26px !important;
        }
        .wrapword {
            white-space: -moz-pre-wrap !important;  /* Mozilla, since 1999 */
            white-space: -pre-wrap;      /* Opera 4-6 */
            white-space: -o-pre-wrap;    /* Opera 7 */
            white-space: pre-wrap;       /* css-3 */
            word-wrap: break-word;       /* Internet Explorer 5.5+ */
            white-space: -webkit-pre-wrap; /* Newer versions of Chrome/Safari*/
            word-break: break-all;
            white-space: normal;
        }

    </style>
    <!--[if true]><xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml><![endif]-->
</head>
<body style="margin: 0; padding: 0;" data-new-gr-c-s-check-loaded="14.981.0">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; min-width: 275px; line-height: normal;" bgcolor="#f7f7f8" background="ia">
        <tbody>
            <tr>
                <td align="center" valign="top" height="30">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" valign="top">
                <table cellpadding="0" cellspacing="0" border="0" width="640" class="table640" style="max-width: 640px; min-width: 275px; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;" bgcolor="#009a88" background="ia">
                    <tbody>
                        <tr>
                            <td align="center" valign="top" style="background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;" bgcolor="#ffffff" background="ia">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="min-width: 100%; max-width: 100%; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" bgcolor="#009a88" background="ia">
                                <tbody>
                                    <tr>
                                        <td valign="top" style="background-repeat: no-repeat; background-position: center bottom;" bgcolor="#621816" background="https://www.ateliersetsuko.com/wp-content/uploads/2021/07/smtp-bg-2.jpg" width="640">
                                        <!--[if gte mso 9]>
<v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:640px;height:250px; background-repeat: no-repeat; background-position: bottom center;">
<v:fill type="frame" src="http://media.campaigner.com/media/0/7501/2019/2019_Email/smtp-bg-2.png" color="#009A88" />
<v:textbox inset="0,0,0,0">
<![endif]-->
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="min-width: 100%; max-width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td valign="top">&nbsp;</td>
                                                    <td valign="top" height="60">&nbsp;</td>
                                                    <td valign="top">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-top: 20px;" align="left" valign="top">&nbsp;</td>
                                                    <td align="center" valign="top"><a href="https://www.ateliersetsuko.com/" name="link atelier"><img src="https://www.ateliersetsuko.com/wp-content/uploads/2021/07/logo-emails.png" alt="Atelier SETSUKO" width="140" style="max-width: 100%; border-width: 0px; border-style: solid;" /></a></td>
                                                    <td align="right" valign="top">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td align="center" valign="top">&nbsp;</td>
                                                    <td align="center" valign="top" height="60">&nbsp;</td>
                                                    <td align="center" valign="top">&nbsp;</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; min-width: 275px; line-height: normal;" bgcolor="#f7f7f8" background="ia">
        <tbody>
            <tr>
                <td align="center" valign="top">
                <table cellpadding="0" cellspacing="0" border="0" width="640" class="table640" style="max-width: 640px; min-width: 275px;" bgcolor="#ffffff" background="ia">
                    <tbody>
                        <tr>
                            <td valign="bottom">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; min-width: 275px; line-height: normal;" bgcolor="#f7f7f8" background="ia">
        <tbody>
            <tr>
                <td align="center" valign="top">
                <table cellpadding="0" cellspacing="0" border="0" width="640" class="table640" style="max-width: 640px; min-width: 275px; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;" bgcolor="#ffffff" background="ia">
                    <tbody>
                        <tr>
                            <td class="mob_pad" style="max-width: 25px; min-width: 20px;" width="20">&nbsp;</td>
                            <td align="center" valign="top" style="background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;" bgcolor="#ffffff" background="ia">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="min-width: 100%; max-width: 100%; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" bgcolor="#ffffff" background="ia">
                                <tbody>
                                    <tr>
                                        <td align="center" valign="top">
                                        <table cellpadding="0" cellspacing="0" border="0" width="86%" style="table-layout: fixed; min-width: 86%; max-width: 630px;">
                                            <tbody>
                                                <tr>
                                                    <td valign="top" height="45">&nbsp;</td>
                                                </tr>
                                                


                                                <tr>
                                                    <td align="left" valign="top" class="mob_txt" style="font-size: 18px !important;line-height: 26px !important;font-family: Verdana, Arial, Tahoma, sans-serif;color: #757474;font-weight: 600" height="40">Bonjour {firstname}, </td>
                                                </tr>
                                                <tr>
                                                    <td align="left" valign="top" class="mob_txt wrapword" style="font-size: 16px !important;line-height: 30px !important;font-family: Verdana, Arial, Tahoma, sans-serif;color: #757474;font-weight: 200; max-width: 630px;">
                                                        <!-- CUSTOM EMAIL CONTENT BEGINS HERE -->

														<?php echo $output; ?>
                                                    
                                                        <!-- CUSTOM EMAIL CONTENT ENDS HERE -->

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="left" valign="top" class="mob_txt" style="font-size: 16px !important;line-height: 30px !important;font-family: Verdana, Arial, Tahoma, sans-serif;color: #757474;font-weight: 200"><br>Merci pour votre confiance, et à très bientôt à l'Atelier !<br>Stéphane.<br><br> </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table cellpadding="0" cellspacing="0" border="0" width="86%" style="min-width: 86%; max-width: 86%;">
                                            <tbody>
                                                <tr>
                                                    <td align="center" valign="top" height="30">&nbsp;</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="mob_btn" cellpadding="10" cellspacing="0" border="0" width="240" style="max-width: 290px; min-width: 140px; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; border-radius: 4px;" bgcolor="#1e0d09">
                                            <tbody>
                                                <tr>
                                                    <td align="center" valign="middle" style="font-family: Arial, Verdana, Tahoma, Geneva, sans-serif; color: #ffffff; font-size: 15px; font-weight: normal;" height="30"><a href="https://www.ateliersetsuko.com/espace-eleve/" name="Connexion Espace élève" style="color: #ffffff;" id="auto_assign_link_num_2">Se connecter à l'espace élève</a></td>
                                                </tr>
                                            </tbody>   
                                        </table>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                            <td class="mob_pad" style="max-width: 25px; min-width: 20px;" width="20"><strong>&nbsp;</strong></td>
                        </tr>
                        <tr>
                            <td class="mob_pad" style="max-width: 25px; min-width: 20px;"><strong>&nbsp;</strong></td>
                            <td align="center" valign="top" height="50"><strong>&nbsp;</strong></td>
                            <td class="mob_pad" style="max-width: 25px; min-width: 20px;"><strong>&nbsp;</strong></td>
                        </tr>
                        <tr>
                            <td class="mob_pad" style="max-width: 25px; min-width: 20px;" bgcolor="#1e0d09"><strong>&nbsp;</strong></td>
                            <td align="center" valign="top" bgcolor="#1e0d09"><strong>&nbsp;</strong></td>
                            <td class="mob_pad" style="max-width: 25px; min-width: 20px;" bgcolor="#1e0d09"><strong>&nbsp;</strong></td>
                        </tr>
                    </tbody>
                </table>
                </td>
            </tr>
            <tr>
                <td align="center" valign="top" height="30"><strong>&nbsp;</strong></td>
            </tr>
        </tbody>
    </table>

</body></html>










