<?

function basic_gather($fpath)
{
  $folders = array('assets', 'callbacks', 'lib', 'models', 'views', 'migrations', 'docs', 'tests');
  
  $dirs = glob($fpath.'/*.php');
  foreach($folders as $folder)
  {
    $dirs = array_merge($dirs, rglob($fpath ."/$folder", '*.*'));
  }
  return $dirs;
}