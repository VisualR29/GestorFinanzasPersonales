<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tipos de cuenta (valor guardado en BD => etiqueta en español)
    |--------------------------------------------------------------------------
    */
    'account_types' => [
        'cuenta_corriente' => 'Cuenta corriente / cheques',
        'cuenta_ahorro' => 'Cuenta de ahorro',
        'nomina' => 'Cuenta nómina',
        'tarjeta_credito' => 'Tarjeta de crédito',
        'tarjeta_debito' => 'Tarjeta de débito',
        'efectivo' => 'Efectivo',
        'inversion' => 'Inversiones (bolsa, fondos, CETES)',
        'digital' => 'Billetera digital',
        'prestamo' => 'Préstamo personal',
        'hipoteca' => 'Hipoteca / crédito hipotecario',
        'ahorro_metas' => 'Cuenta de ahorro para metas',
        'negocio' => 'Cuenta del negocio / PYME',
        'otro' => 'Otro',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de categoría (flujo del dinero)
    |--------------------------------------------------------------------------
    */
    'category_types' => [
        'ingreso' => 'Ingreso',
        'gasto' => 'Gasto',
    ],

    /*
    |--------------------------------------------------------------------------
    | Conceptos detallados por tipo de categoría (opcional en BD: campo kind)
    |--------------------------------------------------------------------------
    */
    'category_kinds' => [
        'ingreso' => [
            'salario' => 'Salario / sueldos',
            'honorarios' => 'Honorarios / freelance',
            'negocio' => 'Ventas / negocio propio',
            'bonos' => 'Bonos / comisiones / aguinaldo',
            'intereses' => 'Intereses / dividendos',
            'alquiler_recibido' => 'Renta / alquiler recibido',
            'regalo' => 'Regalo / apoyo familiar',
            'reembolso' => 'Reembolso',
            'venta_activo' => 'Venta de bien (auto, muebles, etc.)',
            'subsidio' => 'Subsidio / beca',
            'otro_ingreso' => 'Otro ingreso',
        ],
        'gasto' => [
            'alimentacion' => 'Alimentación / supermercado',
            'restaurantes' => 'Restaurantes / comida fuera',
            'transporte' => 'Transporte / combustible / uber',
            'vivienda' => 'Vivienda / renta / mantenimiento',
            'servicios' => 'Servicios (luz, agua, gas, internet)',
            'salud' => 'Salud / medicina / gym',
            'educacion' => 'Educación / cursos',
            'entretenimiento' => 'Entretenimiento / streaming / ocio',
            'ropa' => 'Ropa / calzado / cuidado personal',
            'mascotas' => 'Mascotas',
            'viajes' => 'Viajes',
            'ahorro_inversion' => 'Ahorro programado / aportes inversión',
            'deudas' => 'Pago de tarjetas / préstamos',
            'seguros' => 'Seguros',
            'impuestos' => 'Impuestos / tasas',
            'donaciones' => 'Donaciones / caridad',
            'otro_gasto' => 'Otro gasto',
        ],
    ],
];
