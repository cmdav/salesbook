<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateSingleServiceStructure extends Command
{
    //App\Services\UserService\UserRepository 
    protected $signature = 'make:single-service-structure {controller} {model} {namespace} {repository} {method}';
    protected $description = 'Creates a service and repository structure for a given controller with CRUD operations.';

    public function handle()
    {
        $controller = $this->argument('controller');
        $model = $this->argument('model');
        $namespace = $this->argument('namespace');
        $repositoryPath = $this->argument('repository');
        $method = $this->argument('method');

        $baseName = Str::replaceLast('Controller', '', $controller);
        $methodName = lcfirst($baseName);

        $controllerPath = app_path("Http/Controllers/{$namespace}/{$controller}.php");
        $servicesDir = app_path("Services/{$namespace}/{$baseName}Service");
        $controllerNamespace = "App\\Http\\Controllers\\{$namespace}";
        $repositoryNamespace = str_replace('/', '\\', $repositoryPath);
        $repositoryClass = class_basename($repositoryNamespace);

        $this->ensureDirectoryExists($servicesDir);
        $this->ensureDirectoryExists(dirname($controllerPath));
        $this->ensureDirectoryExists(app_path("Http/Requests/{$namespace}"));

        $this->createFile($servicesDir, "{$baseName}Service.php", $this->serviceTemplate($baseName, $namespace, $repositoryNamespace, $repositoryClass, $methodName, $method));
        $this->createController($controllerPath, $controller, $baseName, $namespace, $method);
        $this->createFormRequest($baseName, $namespace);
        $this->updateRoutes($baseName, $namespace, $controller);
        $this->modifyRepository($repositoryPath, $methodName, $method, $model);

        $this->info('Service structure with controller, form request, and routes created successfully.');
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

    protected function serviceTemplate($baseName, $namespace, $repositoryNamespace, $repositoryClass, $methodName, $method)
    {
        $repositoryVariable = lcfirst($repositoryClass);
        return "<?php

namespace App\\Services\\{$namespace}\\{$baseName}Service;

use {$repositoryNamespace};

class {$baseName}Service
{
    protected \${$repositoryVariable};

    public function __construct({$repositoryClass} \${$repositoryVariable})
    {
        \$this->{$repositoryVariable} = \${$repositoryVariable};
    }

    public function {$method}(\$data = null, \$id = null)
    {
        return \$this->{$repositoryVariable}->{$methodName}(\$data, \$id);
    }
}
";
    }

    protected function createController($path, $controller, $baseName, $namespace, $method)
    {
        $serviceVar = lcfirst($baseName) . 'Service';
        $serviceNamespace = "App\\Services\\{$namespace}\\{$baseName}Service\\{$baseName}Service";
        $formRequestNamespace = "App\\Http\\Requests\\{$namespace}\\{$baseName}FormRequest";

        $methodDefinition = $this->getControllerMethodTemplate($method, $baseName);

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

    {$methodDefinition}
}";

        File::put($path, $content);
    }

    protected function getControllerMethodTemplate($method, $baseName)
    {
        $methodTemplate = "";

        switch ($method) {
            case 'index':
                $methodTemplate = "public function index()
    {
        \$data = \$this->{$baseName}Service->{$method}();
        if (\$data) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => \$data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }";
                break;
            case 'show':
                $methodTemplate = "public function show(\$id)
    {
        \$data = \$this->{$baseName}Service->{$method}(\$id);
        if (\$data) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => \$data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }";
                break;
            case 'store':
                $methodTemplate = "public function store({$baseName}FormRequest \$request)
    {
        \$data = \$this->{$baseName}Service->{$method}(\$request->all());
        if (\$data) {
            return response()->json(['success' => true, 'message' => 'Record created successfully', 'data' => \$data], 201);
        }
        return response()->json(['success' => false, 'message' => 'Creation error'], 500);
    }";
                break;
            case 'update':
                $methodTemplate = "public function update({$baseName}FormRequest \$request, \$id)
    {
        \$data = \$this->{$baseName}Service->{$method}(\$request->all(), \$id);
        if (\$data) {
            return response()->json(['success' => true, 'message' => 'Record updated successfully', 'data' => \$data], 200);
        }
        return response()->json(['success' => false, 'message' => 'Update error'], 500);
    }";
                break;
            case 'destroy':
                $methodTemplate = "public function destroy(\$id)
    {
        \$data = \$this->{$baseName}Service->{$method}(\$id);
        if (\$data) {
            return response()->json(['success' => true, 'message' => 'Record deleted successfully'], 200);
        }
        return response()->json(['success' => false, 'message' => 'Deletion error'], 500);
    }";
                break;
        }

        return $methodTemplate;
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
        $route = "Route::apiResource('" . Str::plural(Str::kebab($baseName)) . "', App\\Http\\Controllers\\{$namespace}\\{$controller}::class);\n";
        File::append($routesPath, $route);
    }

    protected function modifyRepository($repositoryPath, $methodName, $method, $model)
    {
        $repositoryClassPath = base_path(str_replace('\\', '/', $repositoryPath) . '.php');
        $methodTemplate = $this->getRepositoryMethodTemplate($methodName, $method, $model);
    
        if (File::exists($repositoryClassPath)) {
            $content = File::get($repositoryClassPath);
            
            // Check if the last character is a closing bracket, remove it if it is
            if (substr(trim($content), -1) === '}') {
                $content = trim($content, "\n} \t\r");
            }
    
            // Append the new method and re-add the closing bracket
            $newContent = $content . "\n\n{$methodTemplate}\n}";
    
            File::put($repositoryClassPath, $newContent);
        }
    }
    

    protected function getRepositoryMethodTemplate($methodName, $method, $model)
    {
        $methodTemplate = "";
    
        switch ($method) {
            case 'index':
                $methodTemplate = "public function {$methodName}()
        {
            return {$model}::paginate(20);
        }";
                break;
            case 'show':
                $methodTemplate = "public function {$methodName}(\$id)
        {
            return {$model}::find(\$id);
        }";
                break;
            case 'store':
                $methodTemplate = "public function {$methodName}(\$data)
        {
            return {$model}::create(\$data);
        }";
                break;
            case 'update':
                $methodTemplate = "public function {$methodName}(\$data, \$id)
        {
            \$model = {$model}::find(\$id);
            if (\$model) {
                \$model->update(\$data);
                return \$model;
            }
            return null;
        }";
                break;
            case 'destroy':
                $methodTemplate = "public function {$methodName}(\$id)
        {
            \$model = {$model}::find(\$id);
            if (\$model) {
                \$model->delete();
                return \$model;
            }
            return null;
        }";
                break;
        }
    
        return $methodTemplate;
    }
    
}
