#! /usr/bin/env php

<?php

class Transaction {
    public float $amount;
    public string $category;
    public string $date;

    public function __construct(float $amount, string $category) {
        $this->amount = $amount;
        $this->category = $category;
        $this->date = date("Y-m-d H:i:s");
    }

    public function toArray(): array {
        return [
            'amount' => $this->amount,
            'category' => $this->category,
            'date' => $this->date
        ];
    }
}

class TransactionManager {
    private string $dataFile = 'data.json';
    private array $data = ['incomes' => [], 'expenses' => []];

    public function __construct() {
        $this->loadData();
    }

    private function loadData(): void {
        if (file_exists($this->dataFile)) {
            $this->data = json_decode(file_get_contents($this->dataFile), true) ?? $this->data;
        }
    }

    private function saveData(): void {
        file_put_contents($this->dataFile, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    public function addIncome(float $amount, string $category): void {
        $income = new Transaction($amount, $category);
        $this->data['incomes'][] = $income->toArray();
        $this->saveData();
    }

    public function addExpense(float $amount, string $category): void {
        $expense = new Transaction($amount, $category);
        $this->data['expenses'][] = $expense->toArray();
        $this->saveData();
    }

    public function viewIncomes(): array {
        return $this->data['incomes'];
    }

    public function viewExpenses(): array {
        return $this->data['expenses'];
    }

    public function viewSavings(): float {
        $income = array_sum(array_column($this->data['incomes'], 'amount'));
        $expense = array_sum(array_column($this->data['expenses'], 'amount'));
        return $income - $expense;
    }

    public function viewCategories(): array {
        $incomeCats = array_unique(array_column($this->data['incomes'], 'category'));
        $expenseCats = array_unique(array_column($this->data['expenses'], 'category'));
        return ['income' => $incomeCats, 'expense' => $expenseCats];
    }
}

function prompt(string $msg): string {
    echo $msg;
    return trim(fgets(STDIN));
}

function menu(): void {
    $manager = new TransactionManager();

    while (true) {
        echo "\n--- Personal Finance Tracker ---\n";
        echo "1. Add income\n";
        echo "2. Add expense\n";
        echo "3. View incomes\n";
        echo "4. View expenses\n";
        echo "5. View savings\n";
        echo "6. View categories\n";
        echo "7. Exit\n";
        $choice = prompt("Enter your option: ");

        switch ($choice) {
            case '1':
                $amount = (float) prompt("Enter income amount: ");
                $category = prompt("Enter income category: ");
                $manager->addIncome($amount, $category);
                echo "Income added successfully.\n";
                break;

            case '2':
                $amount = (float) prompt("Enter expense amount: ");
                $category = prompt("Enter expense category: ");
                $manager->addExpense($amount, $category);
                echo "Expense added successfully.\n";
                break;

            case '3':
                $incomes = $manager->viewIncomes();
                if (count($incomes)) {
                    echo "Incomes:\n";
                    foreach ($incomes as $income) {
                        echo "- {$income['amount']} | {$income['category']} | {$income['date']}\n";
                    }
                } else {
                    echo "No incomes recorded.\n";
                }
                break;

            case '4':
                $expenses = $manager->viewExpenses();
                if (count($expenses)) {
                    echo "Expenses:\n";
                    foreach ($expenses as $expense) {
                        echo "- {$expense['amount']} | {$expense['category']} | {$expense['date']}\n";
                    }
                } else {
                    echo "No expenses recorded.\n";
                }
                break;

            case '5':
                $savings = $manager->viewSavings();
                echo "Total savings: $savings\n";
                break;

            case '6':
                $cats = $manager->viewCategories();
                echo "Income Categories: " . implode(", ", $cats['income']) . "\n";
                echo "Expense Categories: " . implode(", ", $cats['expense']) . "\n";
                break;

            case '7':
                echo "Goodbye!\n";
                exit;

            default:
                echo "Invalid option.\n";
        }
    }
}

menu();
