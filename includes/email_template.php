<?php
/**
 * Generates a full HTML email body using a stylish template.
 *
 * @param string $site_name The name of the website.
 * @param string $email_title The main headline for the email content.
 * @param string $email_content The core HTML content of the email.
 * @return string The complete HTML for the email body.
 */
function generate_email_html($site_name, $email_title, $email_content) {
    $current_year = date("Y");
    // Using an absolute URL for the logo is best for email client compatibility
    $logo_url = 'https://axript.com.ng/assets/images/monogram.png'; 

    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>{$email_title}</title>
        <style>
            body { font-family: 'Quicksand', sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
            .container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; }
            .header { background-color: #1a1a1a; padding: 20px; text-align: center; }
            .header img { max-width: 150px; }
            .content { padding: 30px; }
            .content h1 { color: #1a1a1a; font-size: 24px; }
            .footer { background-color: #2c2c2c; color: #aaaaaa; padding: 20px; text-align: center; font-size: 12px; }
            .footer a { color: #FFD700; text-decoration: none; }
        </style>
    </head>
    <body>
        <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color: #f4f4f4;'>
            <tr>
                <td align='center' style='padding: 20px;'>
                    <table class='container' border='0' cellspacing='0' cellpadding='0'>
                        <!-- Header -->
                        <tr>
                            <td class='header'>
                                <img src='{$logo_url}' alt='{$site_name} Logo'>
                            </td>
                        </tr>
                        <!-- Content -->
                        <tr>
                            <td class='content'>
                                <h1 style='color: #1a1a1a; font-size: 24px;'>{$email_title}</h1>
                                {$email_content}
                            </td>
                        </tr>
                        <!-- Footer -->
                        <tr>
                            <td class='footer'>
                                <p>&copy; {$current_year} {$site_name}. All Rights Reserved.</p>
                                <p>
                                    <a href='#'>Instagram</a> | 
                                    <a href='#'>Pinterest</a> | 
                                    <a href='#'>Facebook</a>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ";
}
