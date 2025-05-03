<?php
/**
 * Script per previsualitzar el correu de benvinguda sense necessitat de crear un usuari
 * Col·loca aquest arxiu a l'arrel del projecte i accedeix des del navegador
 */

// Definir constants necessàries si no estan definides
if (!defined('URLROOT')) {
    define('URLROOT', 'http://localhost/gymIntranet/gymIntranet');
}

// Dades de prova per a l'usuari
$userData = [
    'email' => 'usuari.prova@example.com',
    'fullName' => 'Usuari de Prova',
    'role' => 'user'
];

// HTML del correu de benvinguda (traduït al català)
$subject = "Benvingut/da a Gym Intranet!";

$body = "
<html>
<head>
    <title>Benvingut/da a Gym Intranet!</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; background-color: #f9f9f9; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #150000 0%, #3a0000 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; letter-spacing: 1px; }
        .header img { max-width: 120px; margin-bottom: 15px; }
        .content { padding: 30px; background-color: #ffffff; }
        .welcome-message { font-size: 18px; margin-bottom: 25px; color: #444; text-align: center; }
        .info-box { background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #150000; }
        .benefits { margin: 30px 0; }
        .benefit-item { display: flex; align-items: center; margin-bottom: 15px; }
        .benefit-icon { width: 30px; text-align: center; margin-right: 15px; color: #150000; font-size: 20px; }
        .button-container { text-align: center; margin: 30px 0; }
        .button { display: inline-block; background-color: #150000; color: white; text-decoration: none; padding: 12px 30px; border-radius: 30px; font-weight: bold; transition: background-color 0.3s; }
        .button:hover { background-color: #3a0000; }
        .social-links { text-align: center; margin-top: 30px; }
        .social-links a { display: inline-block; margin: 0 10px; color: #444; text-decoration: none; }
        .social-icon { font-size: 24px; }
        .footer { background-color: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>BENVINGUT/DA A GYM INTRANET!</h1>
        </div>
        <div class='content'>
            <p class='welcome-message'>Hola <strong>{$userData['fullName']}</strong>, ens alegra tenir-te amb nosaltres!</p>
            
            <p>El teu compte ha estat creat amb èxit i ja pots començar a gaudir de tots els beneficis del nostre centre esportiu.</p>
            
            <div class='info-box'>
                <h3>💡 INFORMACIÓ D'ACCÉS</h3>
                <p>Pots iniciar sessió a la nostra plataforma amb les següents dades:</p>
                <p><strong>Email:</strong> {$userData['email']}</p>
                <p><strong>Contrasenya:</strong> La que has establert durant el registre</p>
            </div>
            
            <div class='benefits'>
                <h3>QUÈ POTS FER A LA NOSTRA PLATAFORMA?</h3>
                <div class='benefit-item'>
                    <div class='benefit-icon'>🏋️</div>
                    <div>Reservar classes dirigides amb els nostres millors instructors</div>
                </div>
                <div class='benefit-item'>
                    <div class='benefit-icon'>🎾</div>
                    <div>Reservar pistes esportives per a les teves activitats favorites</div>
                </div>
                <div class='benefit-item'>
                    <div class='benefit-icon'>📊</div>
                    <div>Fer seguiment del teu progrés físic personal</div>
                </div>
                <div class='benefit-item'>
                    <div class='benefit-icon'>📱</div>
                    <div>Accedir a la teva informació des de qualsevol dispositiu</div>
                </div>
            </div>
            
            <div class='button-container'>
                <a href='".URLROOT."/auth/login' class='button'>ACCEDIR ARA</a>
            </div>
            
            <p>Si tens alguna pregunta o necessites ajuda, no dubtis en contactar amb el nostre equip de suport.</p>
            
            <div class='social-links'>
                <p>Segueix-nos a les xarxes socials:</p>
                <a href='#' class='social-icon'>📱</a>
                <a href='#' class='social-icon'>📘</a>
                <a href='#' class='social-icon'>📸</a>
            </div>
        </div>
        <div class='footer'>
            <p>© " . date('Y') . " Gym Intranet. Tots els drets reservats.</p>
            <p>Aquest és un missatge automàtic, si us plau no responguis a aquest correu.</p>
        </div>
    </div>
</body>
</html>";

// Mostrar el correu
echo $body;