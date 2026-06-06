<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MakeFE extends BaseCommand
{
  protected $group       = 'Generators';
  protected $name        = 'make:fe';
  protected $description = 'Generate Full FE (Controller + View + Modal + JS) secara interaktif';

  public function run(array $params)
  {
    // 1. Ambil nama module dari parameter atau prompt jika kosong
    $module = $params[0] ?? CLI::prompt(
      'Masukkan nama module (contoh: user)',
      'required'
    );

    $module = strtolower($module);
    $class  = ucfirst($module);

    // 2. Tanya user mau pakai lazy atau tidak
    $inputUser = CLI::prompt('Gunakan tabel lazy loading? (y/n)');
    $useLazy = strtolower($inputUser ?: 'n');

    $isLazy = ($useLazy === 'y');

    CLI::write("Memproses module: {$class} " . ($isLazy ? "(Lazy Grid)" : "(Standar)"), 'yellow');

    $this->makeController($class, $module);
    $this->makeView($module, $isLazy);
    $this->makeModal($module, $isLazy);

    CLI::write("🔥 Module {$class} siap tempur!", 'green');
  }

  private function makeController($class, $module)
  {
    $path = APPPATH . "Controllers/{$class}Controller.php";

    // Cek folder Controllers
    if (!is_dir(APPPATH . "Controllers")) mkdir(APPPATH . "Controllers", 0777, true);

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
    CLI::write("- Controller created: {$class}Controller.php", 'cyan');
  }

  private function makeView($module, $isLazy)
  {
    $dir  = APPPATH . "Views/{$module}";
    $path = "{$dir}/index.php";

    if (!is_dir($dir)) mkdir($dir, 0777, true);

    // Pilih stub berdasarkan pilihan user
    $stubFile = $isLazy ? 'view_lazygrid.stub' : 'view.stub';
    $stubPath = APPPATH . 'Commands/Stubs/' . $stubFile;

    if (!file_exists($stubPath)) {
      CLI::error("File stub tidak ditemukan: {$stubFile}");
      return;
    }

    $template = file_get_contents($stubPath);
    $template = str_replace('{{module}}', $module, $template);

    file_put_contents($path, $template);
    CLI::write("- View created: {$module}/index.php", 'cyan');
  }

  private function makeModal($module, $isLazy)
  {
    $path = APPPATH . "Views/{$module}/modal.php";

    // Pilih stub berdasarkan pilihan user
    $stubFile = $isLazy ? 'modal_lazygrid.stub' : 'modal.stub';
    $stubPath = APPPATH . 'Commands/Stubs/' . $stubFile;

    if (!file_exists($stubPath)) {
      CLI::error("File stub tidak ditemukan: {$stubFile}");
      return;
    }

    $template = file_get_contents($stubPath);
    $template = str_replace('{{module}}', $module, $template);

    file_put_contents($path, $template);
    CLI::write("- Modal created: {$module}/modal.php", 'cyan');
  }
}
