<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserFormRequest;
use App\Services\UserService\UserService;
use Illuminate\Http\JsonResponse;
use App\Models\ContactForm;
use Exception;

class ContactFormController extends Controller
{
    
    // protected UserService $userService;

   

    // public function __construct(UserService $userService)
    // {
    //     $this->userService = $userService;

        
    // }
    public function index(){
        
       
       return  ContactForm::paginate(20);
    }
    public function show($id){

        return  ContactForm::where("id", $id)->get();
        // if($user =$this->userService->findById($id)){
        //     return response()->json($user);
        // }
        // return response()->json(['message'=>'user not found'], 404);
    }
   // public function store(Request $request)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:55',
            'last_name' => 'required|string|max:55',
            'phone_number' => 'required|string|max:55',
            'email' => 'required|string|email|max:55|unique:contact_forms',
            'message' => 'nullable|string|max:65535', 
        ]);
    
        $contact = ContactForm::FirstOrCreate($validatedData);
    
        return response()->json(['message' => 'Contact saved successfully!', 'data' => $contact]);
    }
   
    
    // public function update($id, Request $request){

    //     $user = $this->userService->updateUserById($id, $request->all());
    //     if($user){
    //         return response()->json(['message' => 'Update successful .'], 200);
    //     }else{
    //         return response()->json(['message' => 'An error occur. Please try again .'], 500);
    //     }

    // }
    // public function destroy($id)
    // {
    //     $this->userService->deleteUser($id);
    //     return response()->json(null, 204);
    // }
   
}
