<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateSingleServiceStructure extends Command
{
    // Define the command signature and description
    protected $signature = 'make:single-service-structure {controller} {model} {namespace} {repository} {method}';
    protected $description = 'Creates a service and repository structure for a given controller with CRUD operations.';

    public function handle()
    {
        // Retrieve the arguments passed to the command
        $controller = $this->argument('controller');
        $model = $this->argument('model');
        $namespace = $this->argument('namespace');
        $repositoryPath = $this->argument('repository');
        $method = $this->argument('method');

        // Determine the base name by removing "Controller" from the controller name
        $baseName = Str::replaceLast('Controller', '', $controller);
        // Convert the base name to a camelCase method name
        $methodName = lcfirst($baseName);

        // Define paths and namespaces for the files to be created
        $controllerPath = app_path("Http/Controllers/{$namespace}/{$controller}.php");
        $servicesDir = app_path("Services/{$namespace}/{$baseName}Service");
        $controllerNamespace = "App\\Http\\Controllers\\{$namespace}";
        $repositoryNamespace = str_replace('/', '\\', $repositoryPath);
        $repositoryClass = class_basename($repositoryNamespace);

        // Ensure that the necessary directories exist
        $this->ensureDirectoryExists($servicesDir);
        $this->ensureDirectoryExists(dirname($controllerPath));
        $this->ensureDirectoryExists(app_path("Http/Requests/{$namespace}"));

        // Create or append the service file with the specified template
        $this->createOrAppendService(
            $servicesDir,
            "{$baseName}Service.php",
            $baseName,
            $namespace,
            $repositoryNamespace,
            $repositoryClass,
            $methodName,
            $method
        );

        // Create or append the controller file
        $this->createOrAppendController($controllerPath, $controller, $baseName, $namespace, $method);

        // Create the form request file
        $this->createFormRequest($baseName, $namespace);

        // Update the API routes file with the new resource route
        $this->updateRoutes($baseName, $namespace, $controller);

        // Modify the repository to include the new method
        $this->modifyRepository($repositoryPath, $methodName, $method, $model);

        // Output success message
        $this->info('Service structure with controller, form request, and routes created successfully.');
    }

    // Ensure that a directory exists, create it if it doesn't
    protected function ensureDirectoryExists($path)
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
    }

    // Create or append the service file
    protected function createOrAppendService($directory, $filename, $baseName, $namespace, $repositoryNamespace, $repositoryClass, $methodName, $method)
    {
        $filePath = $directory . '/' . $filename;
        $repositoryVariable = lcfirst($repositoryClass);
        $verb = $this->getVerbForMethod($method);
        $serviceMethodTemplate = "\n    public function {$method}(\$data = null, \$id = null)
    {
        return \$this->{$repositoryVariable}->{$verb}{$methodName}(\$data, \$id);
    }";

        if (File::exists($filePath)) {
            // Append the new method to the existing service file
            $existingContent = File::get($filePath);
            $existingContent = rtrim($existingContent, "}\n");
            $newContent = $existingContent . "\n}". $serviceMethodTemplate . "\n}";
            File::put($filePath, $newContent);
        } else {
            // Create the service file
            $content = "<?php

namespace App\\Services\\{$namespace}\\{$baseName}Service;

use {$repositoryNamespace};

class {$baseName}Service
{
    protected \${$repositoryVariable};

    public function __construct({$repositoryClass} \${$repositoryVariable})
    {
        \$this->{$repositoryVariable} = \${$repositoryVariable};
    }

    {$serviceMethodTemplate}
}
";
            File::put($filePath, $content);
        }
    }

    // Create or append the controller class file
    protected function createOrAppendController($path, $controller, $baseName, $namespace, $method)
    {
        $serviceVar = lcfirst($baseName) . 'Service';
        $serviceNamespace = "App\\Services\\{$namespace}\\{$baseName}Service\\{$baseName}Service";
        $formRequestNamespace = "App\\Http\\Requests\\{$namespace}\\{$baseName}FormRequest";

        // Generate the method template for the controller
        $methodDefinition = $this->getControllerMethodTemplate($method, $baseName);

        if (File::exists($path)) {
            // Append the new method to the existing controller file
            $existingContent = File::get($path);
            $existingContent = rtrim($existingContent, "}\n");
            $newContent = $existingContent . "\n}" . $methodDefinition . "\n}";
            File::put($path, $newContent);
        } else {
            // Create the controller file
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
    }

    // Get the method template for the specified controller method
    protected function getControllerMethodTemplate($method, $baseName)
    {
        $methodTemplate = "";
        $baseName = lcfirst($baseName);
        switch ($method) {
            case 'index':
                $methodTemplate = "public function index()
    {
        \$data = \$this->{$baseName}Service->{$method}();
         if (!\$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => \$data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }";
                break;
            case 'show':
                $methodTemplate = "public function show(\$id)
    {
        \$data = \$this->{$baseName}Service->{$method}(\$id);
         if (!\$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => \$data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }";
                break;
            case 'store':
                $methodTemplate = "public function store({$baseName}FormRequest \$request)
    {
        \$data = \$this->{$baseName}Service->{$method}(\$request->all());
         if (!\$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record created successfully', 'data' => \$data], 201);
        }
        return response()->json(['success' => false, 'message' => 'Creation error'], 500);
    }";
                break;
            case 'update':
                $methodTemplate = "public function update({$baseName}FormRequest \$request, \$id)
    {
        \$data = \$this->{$baseName}Service->{$method}(\$request->all(), \$id);
         if (!\$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record updated successfully', 'data' => \$data], 200);
        }
        return response()->json(['success' => false, 'message' => 'Update error'], 500);
    }";
                break;
            case 'destroy':
                $methodTemplate = "public function destroy(\$id)
    {
        \$data = \$this->{$baseName}Service->{$method}(\$id);
         if (!\$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record deleted successfully'], 200);
        }
        return response()->json(['success' => false, 'message' => 'Deletion error'], 500);
    }";
                break;
        }

        return $methodTemplate;
    }

    // Create a form request file
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

    // Update the routes/api.php file to include the new controller
    protected function updateRoutes($baseName, $namespace, $controller)
    {
        $routesPath = base_path('routes/api.php');
        $route = "Route::apiResource('" . Str::plural(Str::kebab($baseName)) . "', App\\Http\\Controllers\\{$namespace}\\{$controller}::class);\n";

        // Check if the route already exists
        if (!str_contains(File::get($routesPath), $route)) {
            File::append($routesPath, $route);
        }
    }

    // Modify the repository to include the new method
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
            $newContent = $content . "\n}\n" . $methodTemplate . "\n}";

            File::put($repositoryClassPath, $newContent);
        }
    }

    // Get the repository method template based on the specified method
    protected function getRepositoryMethodTemplate($methodName, $method, $model)
    {
        $verb = $this->getVerbForMethod($method);
        $methodTemplate = "";

        switch ($method) {
            case 'index':
                $methodTemplate = "public function {$verb}{$methodName}()
        {
            return {$model}::paginate(20);
        }";
                break;
            case 'show':
                $methodTemplate = "public function {$verb}{$methodName}(\$id)
        {
            return {$model}::find(\$id);
        }";
                break;
            case 'store':
                $methodTemplate = "public function {$verb}{$methodName}(\$data)
        {
            return {$model}::create(\$data);
        }";
                break;
            case 'update':
                $methodTemplate = "public function {$verb}{$methodName}(\$data, \$id)
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
                $methodTemplate = "public function {$verb}{$methodName}(\$id)
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

    // Get the verb prefix for the method name
    protected function getVerbForMethod($method)
    {
        switch ($method) {
            case 'index':
                return 'get';
            case 'show':
                return 'show';
            case 'store':
                return 'create';
            case 'update':
                return 'update';
            case 'destroy':
                return 'delete';
            default:
                return '';
        }
    }
}
