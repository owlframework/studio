    
    <p style='font-size: 16px; line-height: 1.5; color: #666;'>
        Hello <?= esc($username); ?> <br><br>
        This is a confirmation that the password for your <strong><?= esc($appname); ?></strong> account was recently changed. 
    </p>

    <div style='margin: 30px 0; padding: 20px; background-color: #fff9e6; border: 1px solid #ffeeba; display: inline-block; border-radius: 10px;'>
        <p style='font-size: 14px; color: #f3a81a; margin: 0;'>
            <strong>Security Alert:</strong> If you did <u>not</u> authorize this change, please contact our support team immediately or reset your password to secure your account.
        </p>
    </div>

    <p style='font-size: 14px; color: #999; margin-top: 20px;'>
        For your security, our team will never ask for your password over email or phone. 
    </p>
    
    <a href="<?= esc($link); ?>" style='display: inline-block; padding: 12px 25px; background-color: #f3a81a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px;'>Login to My Account</a>
