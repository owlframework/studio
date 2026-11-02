<?php
require 'feather.php';
$userModel = new User();
$tracker = new SessionTracker();



?>
            <section class="content-body">

                <?php 
               //$userModel->fetchByRoles(['user', 'staff']);
                $users = $userModel->fetchByRoles(['user']);
            ?>

            <h2 class="section-title">All Users</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                             <th>Actions</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                     

                       <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= esc($user->username) ?></td>
                                    <td><?= esc($user->email) ?></td>
                                    <td><?= esc(ucfirst($user->role)) ?></td>
                                    <td>
                                        <span class="badge <?= $user->status === 'active' ? 'active' : 'inactive' ?>">
                                            <?= esc(ucfirst($user->status)) ?>
                                        </span>
                                    </td>
                                    <?php if (isAdmin($access->role) && $user->id !== $access->id): ?>
                                    <td>
                                        <a href="<?= aurl('impersonate'.'?id='. $user->id) ?>" 
                                        class="tablestyle" id="confirm_user">
                                        Login As
                                        </a>
                                    </td>
                                    <?php endif; ?>

                                    <td><a href="<?= aurl('edit_user'.'?id='. $user->id) ?>" class="tablestyle">Edit</a>
                                    </td>
                                    <td><a href="<?= aurl('delete'.'?id='. $user->id) ?>" class="tablestyle" id="delete_user">Delete</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
              

            </section>


        </main>
    </div>
 
  <script nonce="<?= get_nonce(); ?>">
        document.addEventListener("DOMContentLoaded", function () {
        let delete_user = document.getElementById("delete_user");

        delete_user.addEventListener("click", function (e) {
 
            if (!confirm(`Are you sure you want to Delete this user account`)) {
                e.preventDefault(); // stop the link from opening
            }
        });
    });
    </script>

    <script nonce="<?= get_nonce(); ?>">
        document.addEventListener("DOMContentLoaded", function () {
        let notice = document.getElementById("confirm_user");

        notice.addEventListener("click", function (e) {
 
            if (!confirm(`Are you sure you want to login this user account`)) {
                e.preventDefault(); // stop the link from opening
            }
        });
    });
    </script>
    
    
    
