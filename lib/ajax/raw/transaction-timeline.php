<?php

    require_once "autoload/transaction.php";
    $Transaction = new Transaction();
    $userManager = new userManager();

    $t           = $Transaction->loadTransactionByID($_POST['transaction_id']);
    $timeline    = $Transaction->timeline($_POST['transaction_id']);

?>


<div class="container">

    <div class="page-header">
        <h3><?= $t['type'] ?> <small class="text-info"><?= $t['status'] ?></small></h3>
    </div>

    <div class="mb-3">
        <form id="tUpdate" name="tUpdate" method="post" enctype="multipart/form-data">
            <div class="col-sm-12 mb-3" id="receipt">
                <label for="doc">New Document /<small>Up to 3 file</small></label>
                <div class="custom-file my-1">
                    <input type="file" class="custom-file-input" id="doc" name="doc[]">
                    <label class="custom-file-label" for="doc">Choose your Receipt</label>
                </div>
                <span style="display: none" data-max="2" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> Add more</span>
            </div>
            <div class="col-sm-12 mb-3" id="comment">
                <label for="inputComment">Comment /<small>Optional</small></label>
                <textarea class="form-control" type="text" id="comment" name="comment"></textarea>
            </div>
            <div class="col-sm-12">
                <input type="hidden" name="transaction_id" value="<?= $_POST['transaction_id'] ?>">
                <input type="hidden" name="user_id" value="<?= $_SESSION['id'] ?>">
                <button class="btn btn-primary"  type="submit">Submit</button>
                <div id="fRes" class="mt-3 alert" style="display: none;"></div>
            </div>
        </form>
    </div>
    <div class="timeline mx-5">

            <div class="alert-light">
                <div class="line text-mutedmt-2"></div>
                <div class="separator text-muted">
                    <time><?= $t['created_at'] ?></time>
                </div>
            </div>


            <div class="panel-body">
                <div class="rounded-circle alert-primary ml-n4" style="width: 50px;height: 50px;padding:12px 0 0 15px">
                    <i class="fa fa-2x fa-child"></i>
                </div>
            </div>
            <article class="panel panel-danger panel-outline mt-n5 mb-3">
                <strong class="border-bottom">START</strong>
                <blockquote class="quote-card">
                    <cite class="rounded-top  alert-primary  px-2 py-1"><?= $userManager->getCustom($t['created_by'],'username')['username'] ?></cite>
                    <div id="tActive" class="card mini-stat border-secondary">
                        <div class="card-body mini-stat-img">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-uppercase mb-3 text-secondary">Type</h6>
                                    <h4 class="mb-4 text-primary"><?= $t['type'] ?></h4>
                                </div>
                                <div class="col">
                                    <h6 class="text-uppercase mb-3 text-secondary">Amount</h6>
                                    <h4 class="mb-4">$ <?= $t['amount'] ?></h4>
                                </div>
                                <div class="col">
                                    <h6 class="text-uppercase mb-3 text-secondary">Status</h6>
                                    <h4 class="mb-4 text-warning"><?= $t['status'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </blockquote>
            </article>

            <?php
             foreach ($timeline as $k => $item) {
            ?>

             <div class="panel-body">
                 <?php $bg = ($item['doc'] == 'doc') ?  'info' : 'warning'; ?>
                 <div class="rounded-circle alert-<?= $bg ?> ml-n4" style="width: 50px;height: 50px;padding:12px 0 0 15px">
                 <?= $bg = ($item['doc'] == 'doc') ?  '<i class="fa fa-2x fa-file-image"></i>' : '<i class="fa fa-2x fa-comment"></i>'; ?>
                 </div>
             </div>
            <article class="panel panel-danger panel-outline mt-n5 mb-3">
                <strong class="border-bottom"><?= strtoupper($item['doc']) ?></strong>
                <?php if ($item['doc'] == 'comment') { ?>
                    <blockquote class="quote-card">
                        <span class="float-right"><?= $item['created_at'] ?></span>
                        <cite class="rounded-top alert-warning px-2 py-1"><?= $userManager->getCustom($item['created_by'],'username')['username'] ?></cite>
                        <p class="rounded-bottom card p-2"><?= $item['filename'] ?></p>
                    </blockquote>
                <?php } else if ($item['doc'] == 'doc') { ?>
                    <blockquote class="quote-card">
                        <span class="float-right"><?= $item['created_at'] ?></span>
                        <cite class="rounded-top alert-info px-2 py-1"><?= $userManager->getCustom($item['created_by'],'username')['username'] ?></cite>
                        <p class="rounded-bottom card p-2">
                            <a href="media/transaction/<?= $item['filename'] ?>" target="_blank">
                                <img src="media/transaction/<?= $item['filename'] ?>" class="img-thumbnail w-25">
                            </a>
                        </p>
                    </blockquote>
                <?php } ?>
            </article>

        <?php
            }
        ?>
    </div>