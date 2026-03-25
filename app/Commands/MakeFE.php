<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MakeFE extends BaseCommand
{
  protected $group = 'Generators';
  protected $name = 'make:fe';
  protected $description = 'Generate Full FE (Controller + View + Modal + JS)';

  public function run(array $params)
  {
    if (empty($params)) {
      CLI::error('Gunakan: php spark make:fe namamodule');
      return;
    }

    $module = strtolower($params[0]);
    $class  = ucfirst($module);

    $this->makeController($class, $module);
    $this->makeView($module);
    $this->makeModal($module);

    CLI::write("🔥 Module {$class} siap tempur!", 'green');
  }

  private function makeController($class, $module)
  {
    $path = APPPATH . "Controllers/{$class}Controller.php";

    $template = <<<PHP
    <?php

    namespace App\Controllers;

    use App\Controllers\BaseController;

    class {$class}Controller extends BaseController
    {
        public function index(): string
        {
            return view('{$module}/index');
        }
    }
    PHP;

    file_put_contents($path, $template);
  }

  private function makeView($module)
  {
    $dir = APPPATH . "Views/{$module}";
    $path = "{$dir}/index.php";

    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $template = file_get_contents(APPPATH . 'Commands/Stubs/view.stub');
    $template = str_replace('{{module}}', $module, $template);

    file_put_contents($path, $template);
  }

  private function makeModal($module)
  {
    $path = APPPATH . "Views/{$module}/modal.php";

    $template = file_get_contents(APPPATH . 'Commands/Stubs/modal.stub');
    $template = str_replace('{{module}}', $module, $template);

    file_put_contents($path, $template);
  }
}
