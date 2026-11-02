<?php defined('ROOTPATH') OR die("Direct access denied."); ?>

<p style="font-size: 16px; line-height: 1.5; color: #666; text-align: center;">
    Hello and welcome to <strong><?= esc($appname); ?></strong>! We're excited to have you on board. 
    To finish setting up your account, please use the verification code below:
</p>

<div style="text-align: center; margin: 30px 0;">
    <div style="padding: 20px; background-color: #f9f9f9; border: 2px dashed #f3a81a; display: inline-block; border-radius: 10px;">
        <span style="font-size: 36px; font-weight: bold; color: #333; letter-spacing: 10px; padding-left: 10px;">
            <?= esc($codeGenerated); ?>
        </span>
    </div>
</div>
 
<p style="font-size: 14px; color: #999; margin-top: 20px; text-align: center;">
    This code will expire in 10 minutes. <br>
    If you did not create an account with <?= esc($appname); ?>, please ignore this email.
</p>