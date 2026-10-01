<?php

namespace Tests\Feature;

use App\Jobs\SendWelcomeEmailJob;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_register_form_renders_in_spanish(): void
    {
        $response = $this->get(route('register.create'));

        $response->assertOk();
        $response->assertSee('Formulario de Registro', false);
        $response->assertSee('[APELLIDO, NOMBRE]', false);
    }

    public function test_empty_payload_rejects_required_fields(): void
    {
        $response = $this->from(route('register.create'))->post(route('register.store'), []);

        $response->assertRedirect(route('register.create'));
        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_short_name_is_rejected(): void
    {
        $response = $this->from(route('register.create'))->post(route('register.store'), [
            'name' => 'Al',
            'email' => 'alumno@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_duplicate_email_shows_custom_message(): void
    {
        User::factory()->create(['email' => 'alumno@gmail.com']);

        $response = $this->from(route('register.create'))->post(route('register.store'), [
            'name' => 'Alumno Demo',
            'email' => 'alumno@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Este correo electrónico ya se encuentra registrado.',
        ]);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_unconfirmed_password_shows_custom_message(): void
    {
        $response = $this->from(route('register.create'))->post(route('register.store'), [
            'name' => 'Alumno Demo',
            'email' => 'nuevo@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'otra-clave',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Las contraseñas ingresadas no coinciden.',
        ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_valid_payload_creates_user_and_dispatches_welcome_job(): void
    {
        Queue::fake([SendWelcomeEmailJob::class]);

        $response = $this->from(route('register.create'))->post(route('register.store'), [
            'name' => 'Alumno Demo',
            'email' => 'nuevo@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('register.create'));
        $response->assertSessionHas('success', '¡Usuario registrado con éxito! Correo de bienvenida encolado.');
        $this->assertDatabaseHas('users', [
            'name' => 'Alumno Demo',
            'email' => 'nuevo@gmail.com',
        ]);

        Queue::assertPushed(SendWelcomeEmailJob::class, function (SendWelcomeEmailJob $job): bool {
            return $job->user->email === 'nuevo@gmail.com';
        });
    }

    public function test_welcome_mailable_includes_the_user_name(): void
    {
        $mailable = new WelcomeUserMail(['name' => 'Alumno Demo']);

        $mailable->assertHasSubject('¡Bienvenido a nuestra plataforma!');
        $mailable->assertSeeInHtml('Alumno Demo');
        $mailable->assertSeeInHtml('Registro completado exitosamente.');
    }
}
