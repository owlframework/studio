<?php

//Check if user is connected/login
require 'feather.php';

$id = $_GET['id'];

//Create User Instance
$userModel = new User();
//User the id to get the user information 
$user = $userModel->first(['id' => $id]);

?>

<section class="content-body">
 <h2 class="section-title">Edit User</h2>
                <div class="admin-card large">
                    
             <?php show_alerts(); ?>

                    <form method="POST"> 
                       

                        <div class="input-group">
                            <label>User Name</label>
                            <input type="HIDDEN" name="id" value="<?= $id; ?>" required>
                            <input type="text" name="username" value="<?= esc($user->username); ?>" required>
                        </div>

                        <div class="input-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?= esc($user->email); ?>" required>
                        </div>
  
                        <div class="input-group">
                            <label>Phone</label>
                            <input type="number" name="phone" value="<?= esc($user->phone); ?>" required>
                        </div>

                        <div class="input-group">
                            <label>Email Verified</label>
                            <input type="text" name="email_verified_at" value="<?= esc($user->email_verified_at) ?? ''; ?>">
                        </div>

                         <div class="input-group">
                            <label>Status</label>
                            <select class="form-control" name="status" required>
                                <option value="<?= esc($user->status) ?? ''; ?>"><?= esc($user->status) ?? ''; ?></option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                                <option value="banned">Banned</option>
                            </select>
                         </div>
 

                        <div class="buttonposition">
                            <button type="submit" class="btn-primary btn-auto">Save Edit</button>
                        </div>
                    </form>
                </div>

            </section>