<?php

namespace Tests\Feature;

use App\Rules\RegistrationRule;
use App\Rules\Uppercase;
use Closure;
use Illuminate\Auth\Events\Validated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\Cursor as PaginationCursor;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as ValidationValidator;
use Tests\TestCase;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

class ValidatorTest extends TestCase
{

    public function testValidator()
    {
        $data = [
            "username" => "mirlani",
            "password" => "rahasia"
        ];

        $rule = [
            "username" => "required",
            "password" => "required"
        ];

        $validator = Validator::make($data, $rule);
        assertNotNull($validator);

        assertTrue($validator->passes());
        assertFalse($validator->fails());
    }
    public function testValidatorInvalid()
    {
        $data = [
            "username" => "",
            "password" => ""
        ];

        $rule = [
            "username" => "required",
            "password" => "required"
        ];

        $validator = Validator::make($data, $rule);
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        // menggunakan getMessagedBag() untuk mendapatkan pesan kesalahan validatenya
        $message = $validator->getMessageBag();
        $message->get("usename");
        Log::info($message->toJson(JSON_PRETTY_PRINT));
        Log::info($message->get("username"));
    }


    public function testValidatorException()
    {
        $data = [
            "username" => "",
            "password" => ""
        ];

        $rule = [
            "username" => "required",
            "password" => "required"
        ];

        $validator = Validator::make($data, $rule);
        assertNotNull($validator);

        try {
            $validator->validate();
            $this->fail("ValidationException not thrown");
        } catch (ValidationException $exception) {
            assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }



    public function testValidatorMultipleRules()
    {
        App::setLocale("id");
        $data = [
            "username" => "lani",
            "password" => "lani"
        ];

        $rule = [
            "username" => "required|min:10|max:20|email",
            "password" => ["required", "min:6", "max:10"]
        ];

        $validator = Validator::make($data, $rule);
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }



    public function testValitData()
    {
        $data = [
            "username" => "lani@gmail.com",
            "password" => "rahasia",
            "admin" => true,
            "hello" => "hello lani"
        ];

        // data yang tidak kita validate maka tidak akan di masukan di validate
        $rule = [
            "username" => "required|min:6|max:20|email",
            "password" => "required|min:5|max:20|"
        ];

        $validator = Validator::make($data, $rule);
        assertNotNull($validator);

        // fungsi validated tidak menggunakan try catch
        $message = $validator->validated();
        Log::info(json_encode($message, JSON_PRETTY_PRINT));

        // fungsi validate harus menggunakan try catch
        try {
            $message = $validator->validate();
            Log::info(json_encode($message, JSON_PRETTY_PRINT));
        } catch (ValidationException $exception) {
            assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }


    public function testValidatorInlineRoles()
    {
        App::setLocale("id");
        $data = [
            "username" => "lani",
            "password" => "lani"
        ];

        $rule = [
            "username" => "required|min:10|max:20|email",
            "password" => ["required", "min:6", "max:10"]
        ];

        $messages = [
            "required" => ":attribute harus di isi",
            "max" => ":attribute minimal :max size",
            "min" => ":attribute minimal :min size",
            "email" => ":attribute harus berupa email "
        ];

        $validator = Validator::make($data, $rule, $messages);
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }


    public function testValidatorAfter()
    {
        App::setLocale("id");
        $data = [
            "username" => "lani@gmail.com",
            "password" => "lani@gmail.com"
        ];

        $rule = [
            "username" => "required|min:10|max:20|email",
            "password" => ["required", "min:6", "max:10"]
        ];

        $validator = Validator::make($data, $rule);
        // fungsi after digunakan ketiak seledai melakukan valadate
        $validator->after(function (ValidationValidator $validators) {
            // kita abil data yang ada di attributnya kemudian kita bandingakn
            $data = $validators->getData();
            if ($data["username"] == $data["password"]) {
                $validators->errors()->add("password", "password tidak boleh sama dengan username");
            }
        });
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }



    public function testValidatorRoles()
    {
        $data = [
            "username" => "lani@gmail.com",
            "password" => "lani@gmail.com"
        ];

        $rule = [
            "username" => ["required", "min:10", "max:20", "email", new Uppercase()],
            "password" => ["required", "min:6", "max:10", new RegistrationRule()]
        ];

        $validator = Validator::make($data, $rule);
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }






    public function testValidatorFuntionRoles()
    {
        $data = [
            "username" => "lani@gmail.com",
            "password" => "lani@gmail.com"
        ];

        $rule = [
            "username" => ["required", "min:10", "max:20", "email", function (string $attribute, string $value, Closure $fail) {
                if (strtoupper($value) != $value) {
                    $fail("$attribute tidak boleh sama dengan username hello");
                }
            }],
            "password" => ["required", "min:6", "max:10", new RegistrationRule()]
        ];

        $validator = Validator::make($data, $rule);
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }


    public function testValidatorRuleClasses()
    {
        $data = [
            "username" => "lani",
            "password" => "lani123@gmail.com"
        ];

        $rule = [
            "username" => ["required", new In(["lani", "mirlani", "araman"])],
            "password" => ["required", Password::min(6)->letters()->numbers()->symbols()->max(20)]
        ];

        $validator = Validator::make($data, $rule);
        assertNotNull($validator);
        assertTrue($validator->passes());
    }

    public function testValidateNestedArray()
    {
        $data = [
            "name" => [
                "first" => "muhammad",
                "last" => "mirlani"
            ],
            "address" => [
                "city" => "Wakatobi",
                "stress" => "Jalan. sombu",
                "country" => "Indonesia"
            ]
        ];

        $rules = [
            "name.first" => ["required", "max:100"],
            "name.last" => ["required", "max:100"],
            "address.city" => ["max:200"],
            "address.stress" => ["max:200"],
            "address.country" => ["required", "max:100"]
        ];

        $validator = Validator::make($data, $rules);
        assertTrue($validator->passes());
    }

    public function testValidateNesteIndexdArray()
    {
        $data = [
            "name" => [
                "first" => "muhammad",
                "last" => "mirlani"
            ],
            "address" => [
                [

                    "city" => "Wakatobi",
                    "stress" => "Jalan. sombu",
                    "country" => "Indonesia"
                ],
                [

                    "city" => "Wakatobi",
                    "stress" => "Jalan. waha",
                    "country" => "Indonesia"
                ]
            ]
        ];

        $rules = [
            "name.first" => ["required", "max:100"],
            "name.last" => ["required", "max:100"],
            "address.*.city" => ["max:200"],
            "address.*.stress" => ["max:200"],
            "address.*.country" => ["required", "max:100"]
        ];

        $validator = Validator::make($data, $rules);
        assertTrue($validator->passes());
    }

    
}
