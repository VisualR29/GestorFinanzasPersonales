<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $palette = ['#4F46E5', '#059669', '#DC2626', '#CA8A04', '#7C3AED', '#EA580C', '#0891B2', '#BE185D'];

        $admin = User::query()->create([
            'name' => 'Administrador Demo',
            'email' => 'admin@finanzas.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
            'phone' => '+52 55 1234 5678',
            'birth_date' => '1988-06-22',
            'city' => 'Ciudad de México',
            'country' => 'México',
            'occupation' => 'Administrador del sistema',
            'bio' => 'Cuenta admin para revisar datos agregados de todos los usuarios en la demo.',
        ]);

        $usersRegular = [
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@example.com',
                'phone' => '+52 81 9001 2233',
                'birth_date' => '1999-02-14',
                'city' => 'Monterrey',
                'country' => 'México',
                'occupation' => 'Desarrolladora web',
                'bio' => 'Perfil demo con gastos de hogar, transporte y trabajo remoto.',
            ],
            [
                'name' => 'Carlos Vega',
                'email' => 'carlos.vega@example.com',
                'phone' => '+52 33 8442 5599',
                'birth_date' => '1995-11-03',
                'city' => 'Guadalajara',
                'country' => 'México',
                'occupation' => 'Docente universitario',
                'bio' => 'Perfil demo con ingresos mixtos (salario + honorarios) y gastos educativos.',
            ],
        ];

        $regularModels = [];
        foreach ($usersRegular as $data) {
            $regularModels[] = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => User::ROLE_USUARIO,
                'email_verified_at' => now(),
                'phone' => $data['phone'],
                'birth_date' => $data['birth_date'],
                'city' => $data['city'],
                'country' => $data['country'],
                'occupation' => $data['occupation'],
                'bio' => $data['bio'],
            ]);
        }

        foreach ([$admin, ...$regularModels] as $user) {
            $accounts = $this->createAccountsForUser($user);
            [$ingresoByKind, $gastoByKind] = $this->createCategoriesForUser($user, $palette);
            $this->seedTransactionsForUser($user, $accounts, $ingresoByKind, $gastoByKind);
        }
    }

    /**
     * @return array<string, Account>
     */
    private function createAccountsForUser(User $user): array
    {
        $definitions = [
            ['nomina', 'Nómina / principal'],
            ['cuenta_ahorro', 'Ahorro emergencias'],
            ['tarjeta_debito', 'Débito día a día'],
            ['tarjeta_credito', 'Tarjeta de crédito'],
            ['digital', 'Billetera digital'],
            ['efectivo', 'Efectivo'],
        ];

        $accounts = [];
        foreach ($definitions as [$type, $name]) {
            $accounts[$type] = Account::query()->create([
                'user_id' => $user->id,
                'name' => $name,
                'type' => $type,
                'currency' => 'MXN',
                'notes' => 'Cuenta generada para datos de demostración.',
            ]);
        }

        return $accounts;
    }

    /**
     * @return array{0: array<string, Category>, 1: array<string, Category>}
     */
    private function createCategoriesForUser(User $user, array $palette): array
    {
        $i = 0;
        $ingresoByKind = [];
        foreach (config('finanzas.category_kinds.ingreso', []) as $kind => $label) {
            $ingresoByKind[$kind] = Category::query()->create([
                'user_id' => $user->id,
                'name' => $label,
                'type' => 'ingreso',
                'kind' => $kind,
                'color' => $palette[$i++ % count($palette)],
            ]);
        }

        $gastoByKind = [];
        foreach (config('finanzas.category_kinds.gasto', []) as $kind => $label) {
            $gastoByKind[$kind] = Category::query()->create([
                'user_id' => $user->id,
                'name' => $label,
                'type' => 'gasto',
                'kind' => $kind,
                'color' => $palette[$i++ % count($palette)],
            ]);
        }

        return [$ingresoByKind, $gastoByKind];
    }

    /**
     * Entrada pseudoaleatoria estable por usuario + clave (para montos y fechas).
     */
    private function rng(User $user, string $key, int $min, int $max): int
    {
        $span = $max - $min + 1;

        return $min + (crc32($user->email.'|'.$key) % $span);
    }

    /**
     * @param  array<string, Account>  $accounts
     * @param  array<string, Category>  $ingresoByKind
     * @param  array<string, Category>  $gastoByKind
     */
    private function seedTransactionsForUser(User $user, array $accounts, array $ingresoByKind, array $gastoByKind): void
    {
        $rng = fn (string $key, int $min, int $max): int => $this->rng($user, $key, $min, $max);

        // Nómina quincenal (últimos 5 meses)
        for ($m = 0; $m < 5; $m++) {
            $monthStart = now()->subMonths($m)->startOfMonth();
            foreach ([['day' => 14, 'desc' => 'Nómina — quincena 1'], ['day' => 28, 'desc' => 'Nómina — quincena 2']] as $slot) {
                $day = min($slot['day'], $monthStart->daysInMonth);
                Transaction::query()->create([
                    'account_id' => $accounts['nomina']->id,
                    'category_id' => $ingresoByKind['salario']->id,
                    'amount' => round(15800 + $rng('sal'.$m.$slot['day'], 0, 8500), 2),
                    'description' => $slot['desc'],
                    'occurred_on' => $monthStart->copy()->day($day)->toDateString(),
                ]);
            }
        }

        // Honorarios / proyectos
        for ($m = 1; $m <= 5; $m++) {
            Transaction::query()->create([
                'account_id' => $accounts['cuenta_ahorro']->id,
                'category_id' => $ingresoByKind['honorarios']->id,
                'amount' => round(2800 + $rng('hon'.$m, 0, 6200), 2),
                'description' => 'Honorarios por proyecto '.$m,
                'occurred_on' => now()->subMonths($m)->day(min(18, now()->subMonths($m)->daysInMonth))->toDateString(),
            ]);
        }

        Transaction::query()->create([
            'account_id' => $accounts['nomina']->id,
            'category_id' => $ingresoByKind['bonos']->id,
            'amount' => round(4200 + $rng('bono', 0, 3100), 2),
            'description' => 'Bono trimestral',
            'occurred_on' => now()->subMonths(2)->day(25)->toDateString(),
        ]);

        Transaction::query()->create([
            'account_id' => $accounts['cuenta_ahorro']->id,
            'category_id' => $ingresoByKind['intereses']->id,
            'amount' => round(120 + $rng('int', 0, 380), 2),
            'description' => 'Intereses CETES / inversiones',
            'occurred_on' => now()->subDays(12)->toDateString(),
        ]);

        Transaction::query()->create([
            'account_id' => $accounts['digital']->id,
            'category_id' => $ingresoByKind['reembolso']->id,
            'amount' => round(560 + $rng('ree', 0, 240), 2),
            'description' => 'Reembolso de gastos médicos',
            'occurred_on' => now()->subDays(45)->toDateString(),
        ]);

        // Gastos por categoría (varios movimientos por kind)
        $gastoPlans = [
            'alimentacion' => ['n' => 16, 'min' => 120, 'max' => 1450, 'acct' => 'tarjeta_debito', 'desc' => 'Supermercado / despensa'],
            'restaurantes' => ['n' => 10, 'min' => 85, 'max' => 780, 'acct' => 'tarjeta_credito', 'desc' => 'Comida fuera'],
            'transporte' => ['n' => 14, 'min' => 45, 'max' => 920, 'acct' => 'tarjeta_debito', 'desc' => 'Transporte'],
            'servicios' => ['n' => 8, 'min' => 280, 'max' => 1650, 'acct' => 'nomina', 'desc' => 'Servicios recurrentes'],
            'salud' => ['n' => 7, 'min' => 150, 'max' => 2400, 'acct' => 'tarjeta_credito', 'desc' => 'Salud'],
            'entretenimiento' => ['n' => 9, 'min' => 99, 'max' => 690, 'acct' => 'digital', 'desc' => 'Ocio / streaming'],
            'educacion' => ['n' => 5, 'min' => 350, 'max' => 5200, 'acct' => 'cuenta_ahorro', 'desc' => 'Educación'],
            'vivienda' => ['n' => 5, 'min' => 1800, 'max' => 9800, 'acct' => 'nomina', 'desc' => 'Vivienda'],
            'deudas' => ['n' => 6, 'min' => 450, 'max' => 6200, 'acct' => 'tarjeta_credito', 'desc' => 'Pago tarjeta / préstamo'],
            'ropa' => ['n' => 4, 'min' => 220, 'max' => 2800, 'acct' => 'tarjeta_debito', 'desc' => 'Ropa'],
            'viajes' => ['n' => 3, 'min' => 900, 'max' => 12500, 'acct' => 'tarjeta_credito', 'desc' => 'Viaje'],
            'mascotas' => ['n' => 6, 'min' => 130, 'max' => 2100, 'acct' => 'efectivo', 'desc' => 'Mascotas'],
            'impuestos' => ['n' => 3, 'min' => 800, 'max' => 5400, 'acct' => 'cuenta_ahorro', 'desc' => 'Impuestos'],
        ];

        foreach ($gastoPlans as $kind => $plan) {
            for ($t = 0; $t < $plan['n']; $t++) {
                $daysAgo = $rng('gd'.$kind.$t, 3, 115);
                $amountSpan = max(1, $plan['max'] - $plan['min']);
                Transaction::query()->create([
                    'account_id' => $accounts[$plan['acct']]->id,
                    'category_id' => $gastoByKind[$kind]->id,
                    'amount' => round($plan['min'] + $rng('ga'.$kind.$t, 0, $amountSpan), 2),
                    'description' => $plan['desc'].' #'.($t + 1),
                    'occurred_on' => Carbon::now()->subDays($daysAgo)->toDateString(),
                ]);
            }
        }

        // Cubrir kinds de gasto que no están en $gastoPlans con algunos movimientos
        foreach (array_keys($gastoByKind) as $kind) {
            if (isset($gastoPlans[$kind])) {
                continue;
            }
            for ($t = 0; $t < 4; $t++) {
                $daysAgo = $rng('gx'.$kind.$t, 5, 95);
                Transaction::query()->create([
                    'account_id' => $accounts['tarjeta_debito']->id,
                    'category_id' => $gastoByKind[$kind]->id,
                    'amount' => round(90 + $rng('gxa'.$kind.$t, 0, 2200), 2),
                    'description' => ($gastoByKind[$kind]->name).' — movimiento demo',
                    'occurred_on' => Carbon::now()->subDays($daysAgo)->toDateString(),
                ]);
            }
        }

        // Otros ingresos esporádicos en kinds menos usados
        foreach (['negocio', 'regalo', 'venta_activo', 'subsidio', 'otro_ingreso'] as $idx => $kind) {
            if (! isset($ingresoByKind[$kind])) {
                continue;
            }
            Transaction::query()->create([
                'account_id' => $accounts[$idx % 2 === 0 ? 'digital' : 'cuenta_ahorro']->id,
                'category_id' => $ingresoByKind[$kind]->id,
                'amount' => round(400 + $rng('ing'.$kind, 0, 4800), 2),
                'description' => 'Ingreso demo — '.$ingresoByKind[$kind]->name,
                'occurred_on' => now()->subDays(20 + $idx * 11)->toDateString(),
            ]);
        }
    }
}
