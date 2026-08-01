<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Financial Fraud & Embezzlement',
                'slug' => 'financial-fraud',
                'description' => 'Unethical financial practices, misappropriation of funds, accounting fraud, or bribery.',
                'icon' => 'banknotes',
                'is_active' => true,
            ],
            [
                'name' => 'Workplace Harassment & Bullying',
                'slug' => 'harassment-bullying',
                'description' => 'Verbal abuse, intimidation, discriminatory behavior, or hostile work environment incidents.',
                'icon' => 'user-minus',
                'is_active' => true,
            ],
            [
                'name' => 'Sexual Misconduct',
                'slug' => 'sexual-misconduct',
                'description' => 'Unwanted sexual advances, coercion, assault, or inappropriate sexual behavior.',
                'icon' => 'shield-exclamation',
                'is_active' => true,
            ],
            [
                'name' => 'Safety & Health Hazard',
                'slug' => 'safety-health-hazard',
                'description' => 'OSHA violations, unsafe working conditions, environmental hazards, or failure to follow safety protocols.',
                'icon' => 'exclamation-triangle',
                'is_active' => true,
            ],
            [
                'name' => 'Data Breach & Privacy Violation',
                'slug' => 'data-breach-privacy',
                'description' => 'Unauthorized disclosure of confidential data, HIPAA/GDPR violations, or cybersecurity breaches.',
                'icon' => 'lock-closed',
                'is_active' => true,
            ],
            [
                'name' => 'Discrimination & Bias',
                'slug' => 'discrimination-bias',
                'description' => 'Unfair treatment based on race, gender, religion, age, disability, or national origin.',
                'icon' => 'scale',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
