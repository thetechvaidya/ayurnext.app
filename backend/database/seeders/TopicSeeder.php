<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Topic;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::all();

        foreach ($subjects as $subject) {
            $topics = $this->getTopicsForSubject($subject->name);
            
            foreach ($topics as $topicData) {
                Topic::create([
                    'subject_id' => $subject->id,
                    'name' => $topicData['name'],
                    'description' => $topicData['description'],
                    'is_active' => true,
                ]);
            }
        }
    }

    private function getTopicsForSubject($subjectName)
    {
        $topicsMap = [
            'Ayurveda Fundamentals' => [
                ['name' => 'History and Philosophy', 'description' => 'Origins and philosophical foundations of Ayurveda'],
                ['name' => 'Tridosha Theory', 'description' => 'Understanding Vata, Pitta, and Kapha doshas'],
                ['name' => 'Panchamahabhuta', 'description' => 'Five basic elements theory'],
                ['name' => 'Prakriti and Vikriti', 'description' => 'Constitutional analysis and disease patterns'],
                ['name' => 'Ashtanga Ayurveda', 'description' => 'Eight branches of Ayurveda'],
            ],
            'Anatomy & Physiology' => [
                ['name' => 'Rachana Sharira', 'description' => 'Ayurvedic anatomy concepts'],
                ['name' => 'Kriya Sharira', 'description' => 'Physiological functions in Ayurveda'],
                ['name' => 'Marma Points', 'description' => 'Vital points in the body'],
                ['name' => 'Srotas System', 'description' => 'Channels of circulation'],
                ['name' => 'Ojas, Tejas, Prana', 'description' => 'Subtle essences of body'],
            ],
            'Pharmacology' => [
                ['name' => 'Dravyaguna', 'description' => 'Properties and actions of drugs'],
                ['name' => 'Rasa Panchak', 'description' => 'Five fundamental drug properties'],
                ['name' => 'Karma Classification', 'description' => 'Actions and effects of medicines'],
                ['name' => 'Classical Formulations', 'description' => 'Traditional Ayurvedic medicines'],
                ['name' => 'Herbal Processing', 'description' => 'Preparation methods of drugs'],
            ],
            'Clinical Medicine' => [
                ['name' => 'Nidana Panchak', 'description' => 'Five-fold diagnostic approach'],
                ['name' => 'Roga Vigyan', 'description' => 'Pathology and disease classification'],
                ['name' => 'Chikitsa Principles', 'description' => 'Treatment methodologies'],
                ['name' => 'Panchakarma', 'description' => 'Detoxification and purification'],
                ['name' => 'Preventive Medicine', 'description' => 'Health maintenance and prevention'],
            ],
            'Surgery' => [
                ['name' => 'Sushruta Samhita', 'description' => 'Classical surgical text study'],
                ['name' => 'Yantra Karma', 'description' => 'Surgical instruments and procedures'],
                ['name' => 'Shastra Karma', 'description' => 'Sharp instrument surgeries'],
                ['name' => 'Anushastra Karma', 'description' => 'Para-surgical procedures'],
                ['name' => 'Wound Management', 'description' => 'Healing and post-operative care'],
            ],
            'Pediatrics' => [
                ['name' => 'Kaumara Bhritya', 'description' => 'Ayurvedic pediatric principles'],
                ['name' => 'Child Development', 'description' => 'Growth stages and milestones'],
                ['name' => 'Pediatric Diseases', 'description' => 'Common childhood disorders'],
                ['name' => 'Vaccination in Ayurveda', 'description' => 'Traditional immunization methods'],
                ['name' => 'Child Nutrition', 'description' => 'Dietary guidelines for children'],
            ],
            'Gynecology' => [
                ['name' => 'Stree Roga', 'description' => 'Women\'s health and diseases'],
                ['name' => 'Prasuti Tantra', 'description' => 'Pregnancy and childbirth'],
                ['name' => 'Menstrual Disorders', 'description' => 'Management of menstrual problems'],
                ['name' => 'Fertility and Conception', 'description' => 'Reproductive health optimization'],
                ['name' => 'Postpartum Care', 'description' => 'Post-delivery health management'],
            ],
            'Psychiatry' => [
                ['name' => 'Bhuta Vidya', 'description' => 'Ayurvedic psychiatry and psychology'],
                ['name' => 'Mental Health', 'description' => 'Psychological well-being concepts'],
                ['name' => 'Stress Management', 'description' => 'Techniques for mental balance'],
                ['name' => 'Meditation and Yoga', 'description' => 'Mind-body therapeutic practices'],
                ['name' => 'Psychiatric Disorders', 'description' => 'Mental health conditions in Ayurveda'],
            ],
        ];

        return $topicsMap[$subjectName] ?? [];
    }
}
