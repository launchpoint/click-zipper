<?
restore_error_handler();

$files = array();
foreach($manifests as $module_name => $manifest)
{
  if(isset($manifest['private']) && $manifest['private']) continue;
  $files = array_merge($files, basic_gather($manifest['path']));
}
$files = array_merge($files, basic_gather(KERNEL_FPATH));
$files = array_merge($files, basic_gather(KERNEL_FPATH.'/branches/1'));
$files = array_merge($files, glob(BUILD_FPATH . '/config/*.php'));

$data = event('archive');
foreach($data as $event_name=>$event_data)
{
  if (!array_key_exists('files', $event_data)) continue;
  $files = array_merge($files, $event_data['files']);
}

$final = array();
foreach($files as $fpath)
{
  if($fpath=='') continue;
  if(startswith(CORE_MODULES_FPATH .'/zipper', $fpath)) continue;
  $final[] = substr(ftov($fpath),1);
}

db_dump('db.gz', p('data',true));

$extra = array(
  ROOT_VPATH.'.htaccess',
  ROOT_VPATH.'index.php',
  ROOT_VPATH.'config.php',
  '.'.BUILD_VPATH.'/modules.php',
  '.'.BUILD_VPATH.'/db.gz',
  '.'.BUILD_VPATH.'/config/todo.php'
);
foreach($extra as $f)
{
  $final[] = $f;
}

$old_final = $final;
$final = array();
foreach($old_final as $fname)
{
  if(!file_exists(ROOT_FPATH."/".$fname)) continue;
  $final[] = $fname;
}

$txt = join("\n",$final);

$files_fpath = ZIPPER_CACHE_FPATH . "/files.txt";
file_put_contents($files_fpath, $txt);

$fname = time() . ".tgz";

$todos = <<<TODO
<?
die('Find me in config/todo.php');
die('Ensire PHP 5.2.2+');
die('Ensure SSH access');
die('Ensure access to phpMyAdmin and empty db');
die('chmod -R 755 .');
die('Configure /.htaccess, especially RewriteBase');
die('Configure /environment.php, especially \$domain and \$app_prefix');
if(\$run_mode != 'development') die('Set \$run_mode="development" in /environment.php');
die('Configure /config/database.php');
die('Run /db.sql in phpMyAdmin to populate db');
die('Set \$ft_min_word_len in config/activerecord.php');
die('Configure email_accounts SMTP info in database, or switch to "server" to use internal');
die('Check FireBug or HttpFox for 404's, etc');
TODO;
file_put_contents(BUILD_FPATH.'/config/todo.php', $todos);

$fname = BUILD_FPATH."/$fname";
$cmd = "tar -zcvf $fname --files-from={$files_fpath}";
dprint($cmd,false);
click_exec($cmd);

unlink(BUILD_FPATH.'/config/todo.php');
echo(h("http://username:password@$subdomain.$domain/$fname")."<br/>");
dprint($todos);

