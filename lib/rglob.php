<?

function rglob($path, $pattern)
{
  return explode("\n", shell_exec("find $path -name '$pattern'"));
}

