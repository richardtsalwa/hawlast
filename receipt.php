<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/libraries.php';

use Dompdf\Dompdf;

// Initialize dompdf
// $options = new Options();
// $options->set('isHtml5ParserEnabled', true);
// $options->set('isPhpEnabled', true);

// $dompdf = new Dompdf($options);
$dompdf = new Dompdf();

// HTML content
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    .container {
      max-width: 600px;
      margin: 20px auto;
      border: 1px solid #ccc;
      padding: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>
<div class="container">
  <img src="images/logo.gif" alt="Hawlast Ventures" style="max-width: 100%;">
  <h2>Hawlast Ventures</h2>
  <p>Quees<br>
     Galana road<br>
     Nairobi, 00100<br>
     www.hawlast.com</p>

  <h3>Receipt</h3>
  <p><strong>Receipt Number:</strong>KE0515<br>
     <strong>Date:</strong> February 4, 2024</p>

  <h4>Bill To:</h4>
  <p>Chiral Afrique Tours Travel Ltd<br>
     Forest Court Villas, Suite 11<br>
     Forest Road,Nairobi<br></p>

  <table>
    <thead>
      <tr>
        <th>Description</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Hosting Account Renewal </td>
        <td>1</td>
        <td>6500.00</td>
        <td>Ksh.6500</td>
      </tr>

    </tbody>
  </table>

  <p><strong>Subtotal:</strong> Ksh. 6500.00<br>
     <strong>Total:</strong>Ksh. 6500.00<br></p>

  <h4>Payment Information:</h4>
  <p><strong>Payment Method:</strong> MPESA <br>
     <strong>Ref:SE363JWHGY</strong><br>
      <strong>Date:3rd May. 2024</strong><br></p>

  <p>Thank you for your business!<br>
     Hawlast Ventures appreciates your support.</p>
</div>

</body>
</html>';

// Load HTML to Dompdf
$dompdf->loadHtml($html);

// Set paper size (A4)
$dompdf->setPaper('A4', 'portrait');

// Render PDF (first pass to get total pages)
$dompdf->render();

// Output the PDF content
ob_start(); // Start output buffering
$dompdf->stream('invoice.pdf', array('Attachment' => 0));
$pdfContent = ob_get_clean(); // Get the buffered content

$mail = new PHPMailer(true); // Pass true to enable exceptions
$toEmail = "hawlast@gmail.com";

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.accountsupport.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'support@hawlast.com';
    $mail->Password = 'Send2024#';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('support@hawlast.com', 'Hawlast Ventures');
    $mail->addReplyTo('support@hawlast.com', 'Hawlast Ventures');
    $mail->addAddress('support@hawlast.com', 'Richard Tsalwa');
    $mail->addAddress($toEmail);
    // Add cc or bcc
    $mail->addAttachment($pdfContent, 'receipt.pdf', 'base64', 'application/pdf'); // Attach the PDF

    $mail->isHTML(true);
    $mail->Subject = 'Receipt from Hawlast Ventures';
    $mail->Body    = 'Dear , 

         Please find the attached receipt.
 
         Hawlast Ventures';

    $mail->SMTPDebug = 2; // 2 will enable detailed debugging information

    $mail->send();

    echo "<div class=\"alert alert-success\">Receipt has been sent successfully..</div>";
} catch (Exception $e) {
    echo "<div class=\"alert alert-danger\">Mail could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
}
?>      