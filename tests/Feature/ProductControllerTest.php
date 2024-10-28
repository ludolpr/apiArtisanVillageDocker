<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_Product_Index()
    {
        // Create a role
        $role = Role::firstOrCreate(['name_role' => 'utilisateur'], ['id' => 1]);

        // Create a user
        $user = User::factory()->create(['id_role' => $role->id]);

        // Create a company and associate it with the user
        $company = Company::factory()->create(['id_user' => $user->id]);

        // Create a category
        $category = Category::factory()->create(); // Ensure you have a Category model and factory

        // Create some products and associate them with the company and category
        Product::factory()->count(5)->create([
            'id_company' => $company->id,
            'id_category' => $category->id, // Use the created category ID
        ]);

        // Make a GET request to the index endpoint
        $response = $this->getJson('/api/product');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }


    public function test_Product_Store()
    {
        Storage::fake('public');

        // Create a role
        $role = Role::firstOrCreate(['name_role' => 'utilisateur'], ['id' => 1]);

        // Create a user
        $user = User::factory()->create(['id_role' => $role->id]);

        // Create a company and associate it with the user
        $company = Company::factory()->create(['id_user' => $user->id]);

        // Create a category
        $category = Category::factory()->create();

        // Authenticate the user
        $this->actingAs($user);

        // Product data
        $response = $this->postJson('/api/product', [
            'name_product' => 'Test Product',
            'picture_product' => UploadedFile::fake()->image('product.jpg'),
            'price' => 100,
            'description_product' => 'A sample product.',
            'id_company' => $company->id,
            'id_category' => $category->id, // Use the created category ID
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name_product',
                    'picture_product',
                    'price',
                    'description_product',
                    'id_company',
                    'id_category',
                ],
            ]);
    }


    public function test_Product_Show()
    {
        // Create a role
        $role = Role::firstOrCreate(['name_role' => 'utilisateur'], ['id' => 1]);

        // Create a user
        $user = User::factory()->create(['id_role' => $role->id]);

        // Create a company and associate it with the user
        $company = Company::factory()->create(['id_user' => $user->id]);

        // Create a category
        $category = Category::factory()->create(); // Ensure you have a Category model and factory

        // Create a product associated with the company and category
        $product = Product::factory()->create(['id_company' => $company->id, 'id_category' => $category->id]);

        // Make a GET request to the show endpoint
        $response = $this->getJson("/api/product/{$product->id}");

        // Check the response status
        $response->assertStatus(200);

        // Check that the response contains the product ID
        $response->assertJson(['id' => $product->id]);
    }



    public function test_Product_Update()
    {
        // Create a role
        $role = Role::firstOrCreate(['name_role' => 'utilisateur'], ['id' => 1]);

        // Create a user
        $user = User::factory()->create(['id_role' => $role->id]);

        // Create a company and associate it with the user
        $company = Company::factory()->create(['id_user' => $user->id]);

        // Create a category for the product
        $category = Category::factory()->create();

        // Create a product associated with the company and category
        $product = Product::factory()->create(['id_company' => $company->id, 'id_category' => $category->id]);

        // Authenticate the user
        $this->actingAs($user);

        // Update data for the product
        $data = [
            'name_product' => 'Updated Product',
            'price' => 150,
            'description_product' => 'An updated sample product.',
            'id_company' => $company->id,
            'id_category' => $category->id, // Ensure valid category ID
        ];

        // Send a PUT request with the product update data
        $response = $this->putJson("/api/product/{$product->id}", $data);

        // Verify the response
        $response->assertStatus(200);

        // Check if the product has been updated
        $this->assertDatabaseHas('products', ['name_product' => 'Updated Product']);
    }


    public function test_Product_Destroy()
    {
        // Create a role
        $role = Role::firstOrCreate(['name_role' => 'utilisateur'], ['id' => 1]);

        // Create a user
        $user = User::factory()->create(['id_role' => $role->id]);

        // Create a company and associate it with the user
        $company = Company::factory()->create(['id_user' => $user->id]);

        // Create a category for the product
        $category = Category::factory()->create(); // Ensure Category model and factory exist

        // Create a product associated with the company and category
        $product = Product::factory()->create([
            'id_company' => $company->id,
            'id_category' => $category->id, // Associate the product with the existing category
        ]);

        // Authenticate the user
        $this->actingAs($user);

        // Send a DELETE request to remove the product
        $response = $this->deleteJson("/api/product/{$product->id}");

        // Verify the response
        $response->assertStatus(200);

        // Check if the product has been removed from the database
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}