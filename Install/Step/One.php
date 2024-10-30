<?php
$err = 0;
?>
<p>Usually the correct permissions for folders are <span class="badge text-bg-primary rounded-pill">755</span>, and for files - <span class="badge text-bg-primary rounded-pill">644</span></p>
<ul class="list-group list-group-flush mb-5">
    <?php
        foreach(array_filter(glob(__ROOT . '/*'), 'is_dir') as $dir)
        {
    ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="fw-bold"><?php echo $dir; ?></span>
    <?php 
        if(0755 === (fileperms($dir) & 0777))
        {
    ?>
        <span class="badge text-bg-primary rounded-pill">chmod <?php echo decoct(fileperms($dir) & 0777); ?></span>
    <?php
        } else {
            $err += 1;
    ?>
        <span class="badge text-bg-warning rounded-pill">chmod <?php echo decoct(fileperms($dir) & 0777); ?> => 755</span>
    <?php
        }
    ?>
    
    </li>
    <?php
        foreach(array_filter(glob($dir . '/*'), 'is_dir') as $subdir)
        {
    ?>
            <li class="list-group-item d-flex justify-content-between align-items-center ms-4">
            <span><?php echo $subdir; ?></span>
            <?php 
                if(0755 === (fileperms($subdir) & 0777))
                {
            ?>
                <span class="badge text-bg-primary rounded-pill">chmod <?php echo decoct(fileperms($subdir) & 0777); ?></span>
            <?php
                } else {
                    $err += 1;
            ?>
                <span class="badge text-bg-warning rounded-pill">chmod <?php echo decoct(fileperms($subdir) & 0777); ?> => 755</span>
            <?php
                }
            ?>
            </li>
    <?php
        }
        }
    ?>
</ul>

<p>List of required extensions for php.</p>
<ul class="list-group list-group-flush mb-5">
<?php
$modules = ['intl', 'pdo_pgsql', 'pgsql', 'zip', 'session', 'gd', 'xml'];
foreach($modules as $module) {
?>
<li class="list-group-item d-flex justify-content-between align-items-center">
<span>Extension <span class="fw-bold"><?php echo $module; ?></span></span>
<?php
if(extension_loaded($module))
{
?>
<span class="badge text-bg-primary rounded-pill">is loaded</span>
<?php
} else {
    $err += 1;
?>
<span class="badge text-bg-danger rounded-pill">is not loaded</span>
<?php
}
}
?>
</li>
</ul>
<div class="mb-5">
<?php
if($err == 0)
{
?>
    <a href="?step=2" class="btn btn-primary btn-lg px-4">Step 2</a>
<?php
} else {
?>
    <a href="#" class="btn btn-primary btn-lg px-4 disabled">Step 2</a>
<?php
}
?>
</div>