<?php

namespace Tests\Feature;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
// use GuzzleHttp\Psr7\UploadedFile ----- no need this;
use Illuminate\Http\UploadedFile;
// use Illuminate\Container\Attributes\Storage ----- no need this;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Vérifiez l'environnement de test
        $this->assertEquals('testing', config('app.env'));

        // Créez des rôles par défaut
        Role::factory()->create(['id' => 1, 'name_role' => 'utilisateur']);
        Role::factory()->create(['id' => 2, 'name_role' => 'artisan']);
        Role::factory()->create(['id' => 3, 'name_role' => 'administrateur']);
    }

    /**
     * Test de l'enregistrement d'un utilisateur et de l'envoi d'un e-mail de vérification.
     */

    public function test_register_creates_user_and_sends_verification_email()
    {
        Mail::fake();
        Storage::fake('public');

        // Créez un utilisateur avec le rôle 'utilisateur'
        $role = Role::firstOrCreate(['name_role' => 'utilisateur'], ['id' => 1]);

        // Envoie une requête d'enregistrement avec l'image
        $response = $this->postJson('/api/register', [
            'name_user' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'id_role' => $role->id, // Assignez le bon ID de rôle
            'picture_user' => UploadedFile::fake()->image('test_image.jpg'),
        ]);

        // Vérifiez la réponse
        $response->assertStatus(200)
            ->assertJsonStructure([
                'meta' => ['code', 'status', 'message'],
                'data' => [
                    'user' => ['id', 'name_user', 'email', 'picture_user'], // Incluez le champ de photo
                    'access_token' => ['token', 'type', 'expires_in'], // Ajoutez la structure du token
                ],
            ]);

        // Vérifiez si l'utilisateur est créé et que l'e-mail de vérification est envoyé
        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
            'name_user' => 'John Doe',
        ]);

        // Vérifiez que l'e-mail de vérification a été envoyé
        Mail::assertSent(VerifyEmail::class, function ($mail) {
            return $mail->hasTo('johndoe@example.com');
        });
    }


    /**
     * Test de la connexion avec des informations d'identification valides.
     */

    public function test_login_with_valid_credentials()
    {
        // Créez un utilisateur avec un mot de passe haché
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Tentez de vous connecter avec des informations d'identification valides
        $response = $this->postJson('/api/login', [
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ]);

        // Vérifiez que la réponse est réussie et correspond à la structure attendue
        $response->assertStatus(200)
            ->assertJsonStructure([
                'meta' => ['code', 'status', 'message'],
                'data' => [
                    'user' => ['id', 'name_user', 'email'],
                    'access_token' => ['token', 'type', 'expires_in'],
                ],
            ]);
    }

    /**
     * Test de la connexion avec des informations d'identification invalides.
     */

    public function test_login_with_invalid_credentials()
    {
        // Essayez de vous connecter avec des informations d'identification invalides
        $response = $this->postJson('/api/login', [
            'email' => 'johndoe@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'meta' => [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Invalid email or password',
                ],
            ]);
    }

    /**
     * Test de la déconnexion et de l'invalidation du token.
     */

    public function test_logout_invalidates_token()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Successfully logged out',
                ],
            ]);
    }

    /**
     * Test de la vérification des e-mails avec une URL signée valide.
     */

    public function test_verify_email_with_valid_signature()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $url = URL::temporarySignedRoute(
            'verify',
            Carbon::now()->addMinutes(180),
            ['id' => $user->id]
        );

        $response = $this->get($url);

        // Assure-toi que l'URL est correcte
        $expectedUrl = 'http://localhost:3000/email/verify?status=success';


        $this->assertNotNull($user->fresh()->email_verified_at);
    }


    /**
     * Test de la vérification des e-mails avec une URL signée invalide.
     */

    public function test_verify_email_with_invalid_signature()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $url = route('verify', ['id' => $user->id]) . '?signature=invalid';

        $response = $this->get($url);

        $expectedUrl = ('http://localhost:3000/email/verify?status=invalid_link');
        $this->assertNull($user->fresh()->email_verified_at);
    }

    /**
     * Test de la vérification des e-mails pour un utilisateur déjà vérifié.
     */

    public function test_verify_email_for_already_verified_user()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $url = URL::temporarySignedRoute(
            'verify',
            Carbon::now()->addMinutes(180),
            ['id' => $user->id]
        );

        $response = $this->get($url);

        $expectedUrl = ('http://localhost:3000/email/verify?status=already_verified');
    }
}
