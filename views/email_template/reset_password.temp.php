<?php defined('ROOTPATH') OR die("Direct access denied."); ?>

<p style="font-size: 16px; line-height: 1.5; color: #666; text-align: center;">
    Success! The password for your <strong><?=esc($appname) ?></strong> account has been successfully reset.
</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="<?= esc($link); ?>" style='display: inline-block; padding: 12px 25px; background-color: #f3a81a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px;'>Login to <?=esc($appname) ?></a>
</div>
 
 <div style='margin-top: 30px; padding: 20px; background-color: #fff4f4; border: 1px solid #f9d6d6; border-radius: 8px; text-align: left;'>
<p style='font-size: 14px; color: #d32f2f; margin: 0;'>
    <strong>Didn't make this change?</strong><br>
    If you did not reset your password, please contact our security team immediately to secure your account.
</p>
</div>
 