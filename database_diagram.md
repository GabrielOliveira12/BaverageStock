# Diagrama da Estrutura do Banco de Dados

## Relacionamentos entre as tabelas

```
┌─────────────────────┐
│      SECTIONS       │
│                     │
│ ┌─────────────────┐ │
│ │ id (PK)         │ │
│ │ name            │ │
│ │ type (enum)     │ │
│ │ max_capacity    │ │
│ │ current_volume  │ │
│ │ created_at      │ │
│ │ updated_at      │ │
│ └─────────────────┘ │
└─────────────────────┘
           │
           │ 1:N (FK: section_id)
           ▼
┌─────────────────────┐
│   BEVERAGE_TYPES    │
│                     │
│ ┌─────────────────┐ │
│ │ id (PK)         │ │
│ │ name            │ │
│ │ section_id (FK) │ │
│ │ created_at      │ │
│ │ updated_at      │ │
│ └─────────────────┘ │
└─────────────────────┘
           │
           │ N:M (através de beverage_links)
           ▼
┌─────────────────────┐         ┌─────────────────────┐
│   BEVERAGE_LINKS    │         │     BEVERAGES       │
│   (Tabela Pivot)    │         │                     │
│ ┌─────────────────┐ │         │ ┌─────────────────┐ │
│ │ id (PK)         │ │◄────────┤ │ id (PK)         │ │
│ │ beverage_id(FK) │ │         │ │ name            │ │
│ │ section_id (FK) │ │         │ │ brand           │ │
│ │beverage_type_id │ │         │ │ volume_per_unit │ │
│ │ created_at      │ │         │ │ quantity        │ │
│ │ updated_at      │ │         │ │ total_volume    │ │
│ └─────────────────┘ │         │ │ created_at      │ │
└─────────────────────┘         │ │ updated_at      │ │
           │                     │ └─────────────────┘ │
           │                     └─────────────────────┘
           │ N:1 (todas as FKs)
           ▼
┌─────────────────────┐
│      HISTORY        │
│                     │
│ ┌─────────────────┐ │
│ │ id (PK)         │ │
│ │ operation_type  │ │
│ │ beverage_id(FK) │ │
│ │ section_id (FK) │ │
│ │beverage_type_id │ │
│ │ quantity        │ │
│ │ volume          │ │
│ │ responsible     │ │
│ │ notes           │ │
│ │ created_at      │ │
│ └─────────────────┘ │
└─────────────────────┘
```

## Fluxo de Criação de Bebida

```
1. CRIAR BEBIDA
   ┌─────────────────────┐
   │ POST /beverages     │
   │ {                   │
   │   "name": "Coca",   │
   │   "brand": "Cola",  │
   │   "volume": 0.5,    │
   │   "quantity": 10,   │
   │   "section_id": 2,  │
   │   "type_id": 5      │
   │ }                   │
   └─────────────────────┘
            │
            ▼
2. SISTEMA CRIA
   ┌─────────────────────┐
   │ INSERT INTO         │
   │ beverages           │
   │ (name, brand,       │
   │  volume, quantity)  │
   └─────────────────────┘
            │
            ▼
3. SISTEMA VINCULA
   ┌─────────────────────┐
   │ INSERT INTO         │
   │ beverage_links      │
   │ (beverage_id,       │
   │  section_id,        │
   │  beverage_type_id)  │
   └─────────────────────┘
```

## Relacionamentos Detalhados

### 1. SECTIONS → BEVERAGE_TYPES (1:N)
- Uma seção pode ter vários tipos de bebida
- Um tipo pertence a apenas uma seção
- **Regra de negócio**: Alcoólicas (500L) vs Não-alcoólicas (400L)

### 2. BEVERAGES ↔ SECTIONS (N:M via beverage_links)
- Uma bebida pode estar em várias seções (através dos links)
- Uma seção pode ter várias bebidas

### 3. BEVERAGES ↔ BEVERAGE_TYPES (N:M via beverage_links)
- Uma bebida pode ter vários tipos (através dos links)
- Um tipo pode ser usado por várias bebidas

### 4. BEVERAGE_LINKS (Tabela Pivot Central)
- **Função**: Conecta bebida + seção + tipo
- **Constraint**: Uma bebida só pode ter um link por seção
- **FK Cascade**: Se bebida for deletada, links são removidos
- **FK Restrict**: Seções e tipos não podem ser removidos se em uso

### 5. HISTORY (Rastreamento)
- **Função**: Log de todas as operações (entrada/saída)
- **FKs**: Referencia bebida, seção e tipo para contexto completo
- **Operações**: 'entry' (entrada) | 'exit' (saída)

## Dados Iniciais

```sql
-- Seções padrão
sections:
  1: "Seção Alcoólicas A" (alcoholic, 500L)
  2: "Seção Não Alcoólicas A" (non_alcoholic, 400L)

-- Tipos por seção
beverage_types:
  1: "Cerveja" → section_id: 1
  2: "Vinho" → section_id: 1
  3: "Whisky" → section_id: 1
  4: "Vodka" → section_id: 1
  5: "Refrigerante" → section_id: 2
  6: "Suco Natural" → section_id: 2
  7: "Água" → section_id: 2
  8: "Energético" → section_id: 2
```
