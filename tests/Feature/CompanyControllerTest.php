<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_Company_Index()
    {
        // Create a role
        $role = Role::firstOrCreate(['name_role' => 'utilisateur'], ['id' => 1]);

        // Create a user
        $user = User::factory()->create(['id_role' => $role->id]);

        // Create some companies and associate them with the user
        Company::factory()->count(5)->create(['id_user' => $user->id]);

        // Make a GET request to the index endpoint
        $response = $this->getJson('/api/company');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }



    public function test_Company_Store()
    {
        Storage::fake('public');

        // Créez un rôle
        $role = Role::firstOrCreate(['name_role' => 'utilisateur'], ['id' => 1]);
        // Créez un utilisateur avec le rôle

        $user = User::factory()->create(['id_role' => $role->id]);

        // Authentifiez l'utilisateur
        $this->actingAs($user);

        // Données de la compagnie
        $response = $this->postJson('/api/company', [
            'name_company' => 'Test Company',
            'description_company' => 'A sample company.',
            'picture_company' => UploadedFile::fake()->image('company.jpg'),
            'zipcode' => '12345',
            'phone' => '0123456789',
            'address' => '123 Test St.',
            'siret' => '12345678901234',
            'town' => 'Testville',
            'lat' => 48.8566,
            'long' => 2.3522,
            'id_user' => 1
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name_company',
                    'description_company',
                    'zipcode',
                    'phone',
                    'address',
                    'siret',
                    'town',
                    'lat',
                    'long',
                    'picture_company',
                    'id_user'
                ],
            ]);
        // dd($response);
        return response()->json([
            'meta' => [
                'code' => 201,
                'status' => 'success',
                'message' => 'Company created successfully'
            ]
        ], 201);
    }





    public function test_Company_Show()
    {
        // Créez un rôle
        $role = Role::firstOrCreate(['id' => 1, 'name_role' => 'utilisateur']);

        // Créez un utilisateur avec le rôle
        $user = User::factory()->create(['id_role' => $role->id]);

        // Créez une compagnie associée à l'utilisateur
        $company = Company::factory()->create(['id_user' => $user->id]);

        // Faites une requête GET à l'endpoint show
        $response = $this->getJson("/api/company/{$company->id}");

        // Vérifiez le statut de la réponse
        $response->assertStatus(200);

        // Vérifiez que la réponse contient l'ID de la compagnie
        $response->assertJson(['id' => $company->id]);
    }


    public function test_Company_Update()
    {
        // Créez un rôle
        $role = Role::firstOrCreate(['id' => 1, 'name_role' => 'utilisateur']);

        // Créez un utilisateur avec le rôle
        $user = User::factory()->create(['id_role' => $role->id]);

        // Créez une compagnie associée à l'utilisateur
        $company = Company::factory()->create(['id_user' => $user->id]);

        // Authentifiez l'utilisateur
        $this->actingAs($user);

        // Données de mise à jour de la compagnie
        $data = [
            'name_company' => 'Updated Company',
            'description_company' => 'An updated sample company.',
            'zipcode' => '54321',
            'phone' => '0987654321',
            'address' => '321 Updated St.',
            'siret' => '43210987654321',
            'town' => 'Updatedville',
            'lat' => 48.8566,
            'long' => 2.3522,
        ];

        // Envoie une requête PUT avec les données de mise à jour de la compagnie
        $response = $this->putJson("/api/company/{$company->id}", $data);

        // Vérifiez la réponse
        $response->assertStatus(200);

        // Vérifiez si la compagnie a été mise à jour
        $this->assertDatabaseHas('companies', ['name_company' => 'Updated Company']);
    }


    public function test_Company_Destroy()
    {
        // Créez un rôle
        $role = Role::firstOrCreate(['id' => 2, 'name_role' => 'utilisateur']);

        // Créez un utilisateur avec le rôle
        $user = User::factory()->create(['id_role' => $role->id]);

        // Créez une compagnie associée à l'utilisateur
        $company = Company::factory()->create(['id_user' => $user->id]);

        // Authentifiez l'utilisateur
        $this->actingAs($user);

        // Envoie une requête DELETE pour supprimer la compagnie
        $response = $this->deleteJson("/api/company/{$company->id}");

        // Vérifiez la réponse
        $response->assertStatus(200);

        // Vérifiez si la compagnie a été supprimée de la base de données
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }
}
