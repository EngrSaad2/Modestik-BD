<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add cost to product_variants if not exists
        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                if (!Schema::hasColumn('product_variants', 'cost')) {
                    $table->decimal('cost', 10, 2)->nullable()->after('sale_price');
                }
            });
        }

        // 2. Add cost to order_items if not exists
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'cost')) {
                    $table->decimal('cost', 10, 2)->default(0)->after('price');
                }
            });
        }

        // 3. Accounts table
        if (!Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // e.g. Cash, bKash, Nagad, Bank Account
                $table->enum('type', ['cash', 'bkash', 'nagad', 'bank', 'other'])->default('cash');
                $table->string('account_number')->nullable();
                $table->decimal('opening_balance', 12, 2)->default(0);
                $table->decimal('current_balance', 12, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Suppliers table
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('company_name')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->timestamps();
            });
        }

        // 5. Purchases table
        if (!Schema::hasTable('purchases')) {
            Schema::create('purchases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
                $table->string('invoice_number')->unique();
                $table->date('purchase_date');
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->decimal('due_amount', 12, 2)->default(0);
                $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('unpaid');
                $table->string('payment_method')->nullable();
                $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 6. Purchase Items table
        if (!Schema::hasTable('purchase_items')) {
            Schema::create('purchase_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
                $table->decimal('unit_cost', 10, 2)->default(0);
                $table->integer('quantity')->default(1);
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        // 7. Expenses table
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->string('category'); // product_purchase, packaging, delivery_expenses, courier_charges, employee_salary, owner_salary, marketing, website_expenses, rent, electricity, other
                $table->decimal('amount', 12, 2);
                $table->date('date');
                $table->string('description')->nullable();
                $table->string('payment_method')->nullable();
                $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
                $table->string('attachment')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 8. Investments table
        if (!Schema::hasTable('investments')) {
            Schema::create('investments', function (Blueprint $table) {
                $table->id();
                $table->string('source'); // previous_investment, new_investment, owner_investment, other_investment
                $table->decimal('amount', 12, 2);
                $table->date('date');
                $table->string('description')->nullable();
                $table->string('payment_method')->nullable();
                $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 9. Owner Withdrawals table
        if (!Schema::hasTable('owner_withdrawals')) {
            Schema::create('owner_withdrawals', function (Blueprint $table) {
                $table->id();
                $table->decimal('amount', 12, 2);
                $table->date('date');
                $table->string('reason')->nullable();
                $table->string('payment_method')->nullable();
                $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 10. Salaries table
        if (!Schema::hasTable('salaries')) {
            Schema::create('salaries', function (Blueprint $table) {
                $table->id();
                $table->enum('person_type', ['employee', 'owner'])->default('employee');
                $table->string('person_name');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('salary_month'); // YYYY-MM format e.g. 2026-08
                $table->decimal('salary_amount', 12, 2);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->decimal('due_amount', 12, 2)->default(0);
                $table->date('payment_date')->nullable();
                $table->enum('status', ['paid', 'partial', 'unpaid'])->default('unpaid');
                $table->string('payment_method')->nullable();
                $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 11. Accounting Transactions table (Central Ledger)
        if (!Schema::hasTable('accounting_transactions')) {
            Schema::create('accounting_transactions', function (Blueprint $table) {
                $table->id();
                $table->date('date');
                $table->string('transaction_type'); // order_payment, order_refund, order_delivery, product_purchase, salary, courier_settlement, investment, owner_withdrawal, expense, other_income
                $table->string('reference_number')->nullable();
                $table->text('description');
                $table->decimal('money_in', 12, 2)->default(0);
                $table->decimal('money_out', 12, 2)->default(0);
                $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
                $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
                $table->foreignId('investment_id')->nullable()->constrained('investments')->nullOnDelete();
                $table->foreignId('salary_id')->nullable()->constrained('salaries')->nullOnDelete();
                $table->foreignId('withdrawal_id')->nullable()->constrained('owner_withdrawals')->nullOnDelete();
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_reversed')->default(false);
                $table->foreignId('reversed_transaction_id')->nullable()->constrained('accounting_transactions')->nullOnDelete();
                $table->timestamps();

                $table->index(['date', 'transaction_type']);
                $table->index('account_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_transactions');
        Schema::dropIfExists('salaries');
        Schema::dropIfExists('owner_withdrawals');
        Schema::dropIfExists('investments');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('accounts');

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'cost')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('cost');
            });
        }
        if (Schema::hasTable('product_variants') && Schema::hasColumn('product_variants', 'cost')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('cost');
            });
        }
    }
};
