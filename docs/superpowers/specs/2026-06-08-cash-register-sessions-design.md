# Cash Register Sessions Design

## Overview
Replaces the "Arqueo de Caja" standalone action with a formal "Session" paradigm. Cash registers must be explicitly opened to allow financial operations and closed via a physical cash count (arqueo) at the end of the shift/day.

## Problem Statement
Currently, users can create expenses, collections, and manual movements at any time. "Arqueo" is a retroactive calculation. If movements are made while the physical drawer is "closed", the subsequent physical count will mismatch, creating disjointed cash flow logs.

## Core Flow
1. **Cash Register Dashboard** (`/cash-register`):
   - Replace the static "Arqueo de Caja" button with a dynamic State Banner.
   - **No Active Session**: Banner reads "🔴 Caja Cerrada". Shows a large "Abrir Caja" button.
   - **Active Session**: Banner reads "🟢 Sesión Abierta (Iniciada a las HH:MM con $XX.XX)". Shows a "Cerrar Caja / Arquear" button.
   - Secondary button: "Historial de Sesiones" linking to `/cash-register-closures`.

2. **Strict Operation Blocking (The Golden Rule)**
   - Financial operations that affect physical cash cannot occur without an open session.
   - We will introduce a global validation mechanism (e.g., middleware, form requests, or service-level checks).
   - Blocked actions when closed:
     - Creating manual Cash Register Movements.
     - Creating new Expenses (`/expenses/create`).
     - Processing Collections (`/collections/{id}`).
   - UI enforcement: Buttons to perform these actions should be disabled or intercepted with an alert ("Debes abrir la caja primero").
   - Backend enforcement: Controllers must abort or redirect back with a validation error if a session doesn't exist.

## Components & Data Flow
- **Models**: `CashRegisterClosure` (already updated to support `initial_balance`, `opened_at`, `status`).
- **Views**: 
  - `resources/views/cash_register/index.blade.php` (update the top header area).
  - Add session checking to `ExpenseController`, `CollectionController`, and `CashRegisterController` (or via a new Middleware `CheckOpenCashRegister`).
  - Disable UI buttons in `expenses.index`, `collections.show`, etc.

## Edge Cases
- A user attempts to submit a form that was opened while the session was open, but someone else closed the session in the meantime. The backend enforcement will reject it safely with a clear flash message.
