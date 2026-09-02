CREATE DATABASE remote_jobs_db; USE remote_jobs_db;
CREATE TABLE users (

id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL UNIQUE,
password_hash VARCHAR(255) NOT NULL,
 
role ENUM('seeker', 'employer', 'admin') DEFAULT 'seeker', company_name VARCHAR(255) NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

CREATE TABLE jobs (

id INT AUTO_INCREMENT PRIMARY KEY,

employer_id INT NULL,

title VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL,
description TEXT NOT NULL,

tags VARCHAR(255) DEFAULT 'Remote',

salary VARCHAR(100), logo_url VARCHAR(255),
posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE SET NULL

);

CREATE TABLE applications (

id INT AUTO_INCREMENT PRIMARY KEY,

job_id INT NOT NULL,

applicant_name VARCHAR(255) NOT NULL, applicant_email VARCHAR(255) NOT NULL, portfolio_link VARCHAR(255),
cover_letter TEXT,

applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE

);
 
INSERT INTO jobs (title, company, description, tags, salary, logo_url) VALUES

('Senior Full Stack Engineer', 'CloudSync', 'Lead the development of scalable microservices.', 'Remote, PHP, React', '$130k - $160k', 'https://placehold.co/100x100/1e293b/ffffff?text=CS'),
('AI Research Scientist', 'Aura Intelligence', 'Research and implement advanced LLM architectures.', 'Remote, Python, AI', '$150k - $190k', 'https://placehold.co/100x100/4f46e5/ffffff?text=AI'),
('DevOps Architect', 'NetScale', 'Build robust CI/CD pipelines and manage AWS infrastructure.', 'Remote, AWS, Docker', '$140k - $175k', 'https://placehold.co/100x100/0ea5e9/ffffff?text=NS'),
('Frontend Developer', 'PixelCraft', 'Create stunning, interactive user interfaces.', 'Remote, Vue, CSS', '$90k - $120k', 'https://placehold.co/100x100/ec4899/ffffff?text=PC'),
('Senior Product Manager', 'InnovaTech', 'Drive the product vision and strategy for our core SaaS platform. Work closely with engineering and design teams.', 'Remote, Product, SaaS', '$120k - $150k', 'https://placehold.co/100x100/10b981/ffffff?text=IT'),
('Lead UX/UI Designer', 'Creative Flow', 'Design intuitive and beautiful user experiences for our suite of mobile applications.', 'Remote, Design, Figma', '$110k - $140k', 'https://placehold.co/100x100/8b5cf6/ffffff?text=CF');
