<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Commands;


use Exception;
use Illuminate\Console\Command;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\outro;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom-forms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installation wizard of FFHS Custom Forms.';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle(): void {
        try{
            $this->line("\e[38;5;160m***************************");
            $this->line("*         FFHS Custom Forms        *");
            $this->line("***************************");
            $this->newLine();
            intro('Hello and welcome to the FFHS FFHS Custom Forms Plugin installation wizard.');

            $confirm = confirm(label: 'Do you want to proceed with publishing the migration files for this package package?');
            if($confirm){
                $this->call('vendor:publish', ['--tag' => 'filament-package_ffhs_custom_forms-migrations']);
            }

            $confirm = confirm(label: 'Do you want to proceed with publishing the configuration files for this package?', default: false);
            if($confirm){
                $this->call('vendor:publish', ['--tag' => 'filament-package_ffhs_custom_forms-config']);
            }

            $confirm = confirm(label: 'Would you like to migrate now?');
            if($confirm){
                $this->call('migrate');
            }

            outro('Installation completed!');

        }
        catch (Exception $e){
            $this->error('Something went wrong!');
            // Let's see if someone finds this in here...
            $this->line(
                '⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿
⣿⣿⣿⣿⣿⡏⠉⠛⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿
⣿⣿⣿⣿⣿⣿⠀⠀⠀⠈⠛⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠛⠉⠁⠀⣿
⣿⣿⣿⣿⣿⣿⣧⡀⠀⠀⠀⠀⠙⠿⠿⠿⠻⠿⠿⠟⠿⠛⠉⠀⠀⠀⠀⠀⣸⣿
⣿⣿⣿⣿⣿⣿⣿⣷⣄⠀⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣴⣿⣿
⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠠⣴⣿⣿⣿⣿
⣿⣿⣿⣿⣿⣿⣿⣿⡟⠀⠀⢰⣹⡆⠀⠀⠀⠀⠀⠀⣭⣷⠀⠀⠀⠸⣿⣿⣿⣿
⣿⣿⣿⣿⣿⣿⣿⣿⠃⠀⠀⠈⠉⠀⠀⠤⠄⠀⠀⠀⠉⠁⠀⠀⠀⠀⢿⣿⣿⣿
⣿⣿⣿⣿⣿⣿⣿⣿⢾⣿⣷⠀⠀⠀⠀⡠⠤⢄⠀⠀⠀⠠⣿⣿⣷⠀⢸⣿⣿⣿
⣿⣿⣿⣿⣿⣿⣿⣿⡀⠉⠀⠀⠀⠀⠀⢄⠀⢀⠀⠀⠀⠀⠉⠉⠁⠀⠀⣿⣿⣿
⣿⣿⣿⣿⣿⣿⣿⣿⣧⠀⠀⠀⠀⠀⠀⠀⠈⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢹⣿⣿
⣿⣿⣿⣿⣿⣿⣿⣿⣿⠃⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣿⣿');
            throw $e;
        }
    }
}
