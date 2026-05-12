# Modelo de datos (gestor de finanzas personales)

Puedes copiar el diagrama Mermaid a [draw.io](https://app.diagrams.net/) (Add > Advanced > Mermaid) o a Lucidchart si tu versión lo admite. También puedes replicarlo en MySQL Workbench como modelo lógico.

Los **tipos de cuenta** y los **conceptos de categoría** se documentan en código en `config/finanzas.php` (catálogo ampliable sin cambiar migraciones).

## Diagrama entidad-relación (Mermaid)

```mermaid
erDiagram
    users ||--o{ accounts : "posee"
    users ||--o{ categories : "define"
    accounts ||--o{ transactions : "registra"
    categories ||--o{ transactions : "clasifica"

    users {
        bigint id PK
        string name
        string email UK
        string password
        string role
        string phone
        date birth_date
        string city
        string country
        string occupation
        text bio
        timestamps
    }

    accounts {
        bigint id PK
        bigint user_id FK
        string name
        string type
        string currency
        text notes
        timestamps
    }

    categories {
        bigint id PK
        bigint user_id FK
        string name
        string type
        string kind
        string color
        timestamps
    }

    transactions {
        bigint id PK
        bigint account_id FK
        bigint category_id FK
        decimal amount
        string description
        date occurred_on
        timestamps
    }
```

## Relaciones Eloquent

- `User` **hasMany** `Account`, `Category`.
- `Account` **belongsTo** `User`; **hasMany** `Transaction`.
- `Category` **belongsTo** `User`; **hasMany** `Transaction`.
- `Transaction` **belongsTo** `Account` y **belongsTo** `Category`.

Regla de negocio: la cuenta y la categoría de un movimiento deben pertenecer al mismo usuario (validado en controladores web y API).

Campo opcional **`categories.kind`**: concepto detallado coherente con `type` (ingreso/gasto), definido en `config/finanzas.php`.
