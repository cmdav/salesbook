<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateServiceStructure extends Command
{
    //php artisan make:service-structure  CustomerAppointmentController Appointment Customer
    protected $signature = 'make:service-structure {controller} {model} {namespace}';
    protected $description = 'Creates a service and repository structure for a given controller with CRUD operations.';

    public function handle()
    {
        $controller = $this->argument('controller');
        $model = $this->argument('model');
        $namespace = $this->argument('namespace');

        $baseName = Str::replaceLast('Controller', '', $controller);
        $controllerPath = app_path("Http/Controllers/{$namespace}/{$controller}.php");
        $servicesDir = app_path("Services/{$namespace}/{$baseName}Service");
        $controllerNamespace = "App\\Http\\Controllers\\{$namespace}";

        $this->ensureDirectoryExists($servicesDir);
        $this->ensureDirectoryExists(dirname($controllerPath));
        $this->ensureDirectoryExists(app_path("Http/Requests/{$namespace}"));

        $this->createFile($servicesDir, "{$baseName}Service.php", $this->serviceTemplate($baseName, $model, $namespace));
        $this->createFile($servicesDir, "{$baseName}Repository.php", $this->repositoryTemplate($baseName, $model, $namespace));
        $this->createController($controllerPath, $controller, $baseName, $model, $namespace);
        $this->createFactory($baseName, $model); // Pass the model name
        $this->createFormRequest($baseName, $namespace);
        $this->updateRoutes($baseName, $namespace, $controller);

        $this->info('Service structure with controller, factory, form request, and routes created successfully.');
    }

    protected function ensureDirectoryExists($path)
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
    }

    protected function createFile($directory, $filename, $content)
    {
        $filePath = $directory . '/' . $filename;
        if (!File::exists($filePath)) {
            File::put($filePath, $content);
        }
    }

    protected function serviceTemplate($baseName, $model, $namespace)
    {
        $repositoryVariable = lcfirst($baseName) . 'Repository';
        return "<?php

namespace App\\Services\\{$namespace}\\{$baseName}Service;

use App\\Services\\{$namespace}\\{$baseName}Service\\{$baseName}Repository;

class {$baseName}Service
{
    protected \${$repositoryVariable};

    public function __construct({$baseName}Repository \${$repositoryVariable})
    {
        \$this->{$repositoryVariable} = \${$repositoryVariable};
    }

    public function index()
    {
        return \$this->{$repositoryVariable}->index();
    }

    public function show(\$id)
    {
        return \$this->{$repositoryVariable}->show(\$id);
    }

    public function store(\$data)
    {
        return \$this->{$repositoryVariable}->store(\$data);
    }

    public function update(\$data, \$id)
    {
        return \$this->{$repositoryVariable}->update(\$data, \$id);
    }

    public function destroy(\$id)
    {
        return \$this->{$repositoryVariable}->destroy(\$id);
    }
}
";
    }

    protected function repositoryTemplate($baseName, $model, $namespace)
    {
        return "<?php

namespace App\\Services\\{$namespace}\\{$baseName}Service;

use App\\Models\\{$model};

use Exception;

class {$baseName}Repository
{
    public function index()
    {
          \$model =  {$model}::paginate(20);
         if(\$model){
         return response()->json([ 'success' =>true, 'message' => 'Record retrieved successfully', 'data'=>\$model], 200);
        }
        return response()->json([ 'success' =>false, 'message' => 'No record found', 'data'=>\$model], 404);
    }

    public function show(\$id)
    {
        \$model = {$model}::where('id',\$id)->first();
        if(\$model){
         return response()->json([ 'success' =>true, 'message' => 'Record retrieved successfully', 'data'=>\$model], 200);
        }
        return response()->json([ 'success' =>false, 'message' => 'No record found', 'data'=>\$model], 404);
    }

    public function store(\$data)
    {
        try {
             \$model =  {$model}::create(\$data);
             return response()->json([ 'success' =>true, 'message' => 'Insertion successful', 'data'=>\$model], 200);
        } catch (Exception \$e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . \$e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }
        
    }

    public function update(\$data, \$id)
    {
        try {  
        \$model = {$model}::where('id',\$id)->first();
            if(\$model){
                \$model->update(\$data);
                 return response()->json([ 'success' =>true, 'message' => 'Update successful', 'data'=>\$model], 200);
            }
           
             return response()->json([ 'success' =>false, 'message' => 'Record not found', 'data'=>\$model], 404);
        } catch (Exception \$e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . \$e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }
    }   

    public function destroy(\$id)
    {
        \$model = {$model}::findOrFail(\$id);
        \$model->delete();
        return \$model;
    }
}
";
    }

    protected function createController($path, $controller, $baseName, $model, $namespace)
    {
        $serviceVar = lcfirst($baseName) . 'Service';
        $serviceNamespace = "App\\Services\\{$namespace}\\{$baseName}Service\\{$baseName}Service";
        $formRequestNamespace = "App\\Http\\Requests\\{$namespace}\\{$baseName}FormRequest";

        $content = "<?php

namespace App\\Http\\Controllers\\{$namespace};
use App\\Http\\Controllers\\Controller;
use {$serviceNamespace};
use {$formRequestNamespace};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class {$controller} extends Controller
{
    private \${$serviceVar};

    public function __construct({$baseName}Service \${$serviceVar})
    {
        \$this->{$serviceVar} = \${$serviceVar};
    }

    public function index()
    {
        return \$this->{$serviceVar}->index();
    }

    public function show(\$id)
    {
        return \$this->{$serviceVar}->show(\$id);
    }

    public function store({$baseName}FormRequest \$request)
    {
        return \$this->{$serviceVar}->store(\$request->all());
    }

    public function update({$baseName}FormRequest \$request, \$id)
    {
        return \$this->{$serviceVar}->update(\$request->all(), \$id);
    }

    public function destroy(\$id)
    {
        return \$this->{$serviceVar}->destroy(\$id);
    }
}";

        File::put($path, $content);
    }

    protected function createFactory($baseName, $model)
    {
        $factoryPath = base_path('database/factories/' . $baseName . 'Factory.php');
        $content = "<?php

namespace Database\Factories;

use App\Models\\{$model};
use Illuminate\Database\Eloquent\Factories\Factory;

class {$baseName}Factory extends Factory
{
    protected \$model = {$model}::class;

    public function definition()
    {
        return [
            // Define model properties here
        ];
    }
}";
        File::put($factoryPath, $content);
    }

    protected function createFormRequest($baseName, $namespace)
    {
        $requestPath = app_path("Http/Requests/{$namespace}/{$baseName}FormRequest.php");
        $content = "<?php

namespace App\\Http\\Requests\\{$namespace};

use Illuminate\Foundation\Http\FormRequest;

class {$baseName}FormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Validation rules
        ];
    }
}";
        File::put($requestPath, $content);
    }

    protected function updateRoutes($baseName, $namespace, $controller)
    {
        $routesPath = base_path('routes/api.php');
        $route = "Route::resource('" . Str::plural(Str::kebab($baseName)) . "', App\\Http\\Controllers\\{$namespace}\\{$controller}::class);\n";
        File::append($routesPath, $route);
    }
}
