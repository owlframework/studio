<?php defined('ROOTPATH') OR die("Direct access denied."); ?>

<p>Hello <?= esc($username); ?>,</p>
<p>We received a request to reset your <strong><?= esc($appname); ?></strong> account password. Click the button below to proceed. This link will expire in 1 hour.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="<?= esc($link); ?>" style='display: inline-block; padding: 12px 25px; background-color: #f3a81a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px;'>Reset My Password</a>

</div>


<p>If you did not request this, please ignore this email or contact support if you have concerns.</p>
<hr style="border: none; border-top: 1px solid #eeeeee; margin: 20px 0;">
<p style="font-size: 12px; color: #999999;">If the button doesn't work, copy and paste this link into your browser:<br>
<span style="color: #f3a81a;"><?= $link; ?></span></p>