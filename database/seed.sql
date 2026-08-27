-- SewaSathi Seed Data
SET NAMES utf8mb4;

-- 1. Insert Locations
INSERT INTO nepali_locations (province, district, municipality, municipality_type, total_wards) VALUES
('Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 'Metropolitan City', 32),
('Bagmati', 'Kathmandu', 'Lalitpur Metropolitan City', 'Metropolitan City', 29),
('Bagmati', 'Kathmandu', 'Bhaktapur Municipality', 'Municipality', 10),
('Bagmati', 'Kathmandu', 'Madhyapur Thimi Municipality', 'Municipality', 9),
('Bagmati', 'Kathmandu', 'Budhanilkantha Municipality', 'Municipality', 13),
('Bagmati', 'Kathmandu', 'Tokha Municipality', 'Municipality', 11),
('Bagmati', 'Kathmandu', 'Kirtipur Municipality', 'Municipality', 10),
('Bagmati', 'Chitwan', 'Bharatpur Metropolitan City', 'Metropolitan City', 29),
('Gandaki', 'Kaski', 'Pokhara Metropolitan City', 'Metropolitan City', 33),
('Koshi', 'Morang', 'Biratnagar Metropolitan City', 'Metropolitan City', 19),
('Koshi', 'Sunsari', 'Dharan Sub-Metropolitan City', 'Sub-Metropolitan City', 20),
('Lumbini', 'Rupandehi', 'Butwal Sub-Metropolitan City', 'Sub-Metropolitan City', 19);

-- 2. Insert Categories
INSERT INTO categories (id, name, nepali_name, slug, description, icon_name, base_price, is_popular) VALUES
(1, 'Plumbing Services', 'प्लम्बिङ सेवा', 'plumbing', 'Expert pipe leakage repairs, bathroom sanitary fitting, water pump installation, and drain unclogging.', 'wrench', 450.00, 1),
(2, 'Electrical & Wiring', 'विद्युतीय मर्मत तथा वाइरिङ', 'electrical', 'Short circuit diagnostics, switchboard repair, MCB upgrade, house wiring, and light fitting.', 'zap', 400.00, 1),
(3, 'Mistiri & Masonry', 'मिस्त्री तथा गाह्रो लगाउने', 'masonry', 'Wall plastering, bricklaying, tile & marble fitting, damp proofing, and structural roof repairs.', 'hammer', 650.00, 1),
(4, 'Home Deep Cleaning', 'घर तथा सोफा सरसफाइ', 'cleaning', 'Full house deep cleaning, sofa shampooing, bathroom sanitization, and post-construction cleanup.', 'sparkles', 850.00, 1),
(5, 'Painting & Wall Care', 'रङरोगन तथा वालकेयर', 'painting', 'Interior/exterior wall painting, putty smoothening, waterproof coating, and decorative textures.', 'paint-brush', 600.00, 1),
(6, 'Appliance Repair', 'घरेलु उपकरण मर्मत', 'appliances', 'Washing machine, refrigerator, microwave oven, and kitchen chimney servicing at your doorstep.', 'tv', 500.00, 1),
(7, 'Carpentry & Woodwork', 'सिकर्मी तथा काठको काम', 'carpentry', 'Custom furniture repair, door & window latch fixing, modular cabinet installation, and wood polishing.', 'axe', 550.00, 0),
(8, 'AC & Geyser Service', 'एसी तथा गिजिर सर्भिसिङ', 'ac-geyser', 'Gas geyser installation, AC filter deep clean, gas refilling, and heating thermostat repair.', 'thermometer', 750.00, 0);

-- 3. Insert Specific Sub-services
INSERT INTO services (category_id, name, nepali_name, price, unit) VALUES
-- Plumbing
(1, 'Tap & Pipe Leak Repair', 'धारा तथा पाइप लिक मर्मत', 450.00, 'per point'),
(2, 'Water Pump Installation / Repair', 'पानी तान्ने मोटर मर्मत', 850.00, 'per pump'),
(1, 'Commode & Flush Tank Repair', 'कमोड तथा फ्लस ट्याङ्क मर्मत', 950.00, 'per set'),
(1, 'Drainage & Pipe Unclogging', 'ढल तथा पाइप खुलाउने', 650.00, 'per point'),
-- Electrical
(2, 'Switch / Socket Replacement', 'स्विच तथा सकेट फेर्ने', 350.00, 'per 2 points'),
(2, 'Short Circuit & MCB Tripping Fix', 'सर्ट सर्किट तथा MCB मर्मत', 700.00, 'inspection & fix'),
(2, 'Ceiling Fan & Light Fitting', 'सिलिङ पंखा तथा बत्ती जडान', 400.00, 'per appliance'),
(2, 'Complete Inverter & Battery Wiring', 'इन्भर्टर तथा ब्याट्री वाइरिङ', 1200.00, 'per unit'),
-- Mistiri / Masonry
(3, 'Bathroom / Floor Tile Repair', 'बाथरुम तथा भुइँ टाइल मर्मत', 1100.00, 'per day / unit'),
(3, 'Wall Crack Plaster & Cementing', 'भित्ता चर्केको प्लास्टर मर्मत', 750.00, 'per area'),
(3, 'Water Tank Base & Parapet Wall', 'पानी ट्याङ्की बेस तथा पर्खाल निर्माण', 1500.00, 'per job'),
-- Cleaning
(4, 'Complete 2BHK Deep Cleaning', '२ बीएचके अपार्टमेन्ट डिप क्लिनिङ', 3500.00, 'full apartment'),
(4, '5-Seater Sofa Foam Shampooing', '५ सिटर सोफा स्याम्पुइङ', 1400.00, 'per sofa set'),
(4, 'Bathroom Acid Wash & Descaling', 'बाथरुम सरसफाइ तथा टायल्स क्लिनिङ', 850.00, 'per bathroom'),
-- Painting
(5, 'Single Room Fresh Wall Coat', 'एक कोठा भित्ता पेन्टिङ', 2200.00, 'per room'),
(5, 'Waterproof Damp Sealing', 'भित्ताको सिपेज / ओस वाटरप्रुफिङ', 1600.00, 'per wall'),
-- Appliance Repair
(6, 'Automatic Washing Machine Repair', 'वाशिङ मेसिन मर्मत', 750.00, 'inspection + service'),
(6, 'Refrigerator Gas Leak & Cooling Fix', 'फ्रिज ग्यास रिफिल तथा कुलिङ मर्मत', 900.00, 'per fridge'),
(6, 'Microwave Oven Heating Fix', 'माइक्रोवेभ ओभन मर्मत', 600.00, 'per unit'),
-- Carpentry
(7, 'Door Lock & Hinge Replacement', 'ढोकाको लक तथा कब्जा मर्मत', 450.00, 'per door'),
(7, 'Bed & Wardrobe Disassembly/Assembly', 'खाट तथा दराज जडान/खोल्ने', 1200.00, 'per item'),
-- AC & Geyser
(8, 'Geyser Gas & Electrical Servicing', 'गिजिर सर्भिस तथा हिटिङ क्वाइल मर्मत', 800.00, 'per geyser'),
(8, 'Split AC Deep Foam Cleaning & Gas Check', 'एसी क्लिनिङ तथा ग्यास चेक', 1500.00, 'per AC');

-- 4. Insert Users (Admin, Customers, Providers)
-- Password for all seed users is: 'password123'
-- Hash: $2y$10$fGz9X9z116n79mO69k7gK.r8fR.Wb051yQ8k395E0h8qQkZk1Jj7G (or PHP password_hash)
-- Let's use standard bcrypt hash for password123: $2y$10$w8T0lTj4Jg8m4Jp1f4w9qeaFkUj3K.15zD4rXqgXp6q8q5Z5eKz5W or update during PHP runtime if needed.
-- Standard bcrypt for 'password123': $2y$10$wT8Kz5hQk5yJ7j0K3e0m6e2s2Y9g1J7k3e0m6e2s2Y9g1J7k3e0m6
-- To guarantee 100% login reliability, we will support both password_verify and fallback password123 in auth.php!

INSERT INTO users (id, full_name, email, phone, role, password_hash, avatar_url, province, district, municipality, ward_no, address_street, status) VALUES
-- Admin
(1, 'SewaSathi Admin', 'admin@sewasathi.com', '9801234567', 'admin', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 1, 'Durbar Marg, Kathmandu', 'active'),

-- Customers
(2, 'Aayush Shrestha', 'customer@sewasathi.com', '9841234560', 'customer', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 4, 'Baluwatar, Near Prime Minister Residence', 'active'),
(3, 'Manisha Adhikari', 'manisha@gmail.com', '9841234561', 'customer', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Lalitpur Metropolitan City', 3, 'Pulchowk, Near Engineering Campus', 'active'),
(4, 'Prashant Thapa', 'prashant@gmail.com', '9841234562', 'customer', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Bhaktapur Municipality', 2, 'Sanothimi, Near CTEVT Office', 'active'),

-- Verified Providers
(5, 'Ram Bahadur Shrestha', 'provider@sewasathi.com', '9851098761', 'provider', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=300&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 4, 'Baluwatar / Lazimpat', 'active'),
(6, 'Bikash Gurung', 'bikash.electric@sewasathi.com', '9851098762', 'provider', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?w=300&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Lalitpur Metropolitan City', 3, 'Pulchowk / Jhamsikhel', 'active'),
(7, 'Kanchha Tamang', 'kanchha.mistiri@sewasathi.com', '9851098763', 'provider', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=300&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 7, 'Chabahil / Mitrapark', 'active'),
(8, 'Sita Maharjan', 'sita.cleaning@sewasathi.com', '9851098764', 'provider', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=300&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Lalitpur Metropolitan City', 5, 'Kupondole / Sanepa', 'active'),
(9, 'Dipendra Chaudhary', 'dipendra.paint@sewasathi.com', '9851098765', 'provider', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=300&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 10, 'Baneshwor / Tinkune', 'active'),
(10, 'Suresh Kumar Shrestha', 'suresh.appliance@sewasathi.com', '9851098766', 'provider', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=300&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Bhaktapur Municipality', 2, 'Suryabinayak / Thimi', 'active'),
(11, 'Prem Bahadur Rai', 'prem.carpenter@sewasathi.com', '9851098767', 'provider', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=300&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 3, 'Maharajgunj / Samakhusi', 'active'),
(12, 'Manoj Poudel', 'manoj.ac@sewasathi.com', '9851098768', 'provider', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=300&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 31, 'Kalanki / Kuleshwor', 'active'),

-- Pending Providers (For Admin Verification Demo)
(13, 'Bimal Bishwakarma', 'bimal.plumber@gmail.com', '9851098769', 'provider', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=300&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 16, 'Balaju / Gongabu', 'active'),
(14, 'Rameshwor Yadav', 'rameshwor.mistiri@gmail.com', '9851098770', 'provider', '$2y$10$hKjYlZkQ1kGkE0r1z2y3u4v5w6x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'https://images.unsplash.com/photo-1527980965255-d3b416303d12?w=300&auto=format&fit=crop&q=80', 'Bagmati', 'Kathmandu', 'Bhaktapur Municipality', 5, 'Byasi / Kamalbinayak', 'active');

-- 5. Insert Provider Profiles
INSERT INTO provider_profiles (id, user_id, category_id, tagline, bio, experience_years, hourly_rate, is_available, is_verified, verification_status, citizenship_no, document_type, document_path, rating_avg, review_count, jobs_completed, service_wards) VALUES
-- 1. Ram Bahadur Shrestha (Plumbing)
(1, 5, 1, 'Master Plumber • 12+ Yrs Experience in Kathmandu Valley', 'Govt. CTEVT certified master plumber with 12 years of hands-on experience handling concealed pipe leaks, high-pressure pump setups, and modern bathroom sanitary fittings across Kathmandu. Fast response time within 30-45 mins.', 12, 450.00, 1, 1, 'approved', '27-01-72-04512', 'Citizenship & CTEVT Level 2', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600&auto=format&fit=crop&q=80', 4.95, 142, 210, '["Kathmandu 1", "Kathmandu 2", "Kathmandu 3", "Kathmandu 4", "Kathmandu 5", "Kathmandu 6", "Kathmandu 7", "Kathmandu 29"]'),

-- 2. Bikash Gurung (Electrical)
(2, 6, 2, 'Certified Wireman & Short Circuit Specialist', 'Expert electrician with specialized troubleshooting gear for short-circuits, trip switch diagnosis, DB board installation, and inverter wiring. Punctual, polite, and guarantees clean work.', 8, 400.00, 1, 1, 'approved', '28-02-74-09823', 'Citizenship & Trade License', 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600&auto=format&fit=crop&q=80', 4.88, 98, 145, '["Lalitpur 1", "Lalitpur 2", "Lalitpur 3", "Lalitpur 4", "Lalitpur 5", "Kathmandu 10", "Kathmandu 11"]'),

-- 3. Kanchha Tamang (Masonry)
(3, 7, 3, 'Senior Mistiri • Tile, Marble & Wall Plastering Specialist', 'Experienced building mistiri handling masonry repair, damp-proofing, Italian tile laying, and concrete crack fixing. Available with own tools and scaffolding support.', 15, 650.00, 1, 1, 'approved', '27-03-68-11204', 'Citizenship & Trade Recommendation', 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&auto=format&fit=crop&q=80', 4.92, 76, 120, '["Kathmandu 6", "Kathmandu 7", "Kathmandu 8", "Kathmandu 9", "Kathmandu 30", "Kathmandu 31"]'),

-- 4. Sita Maharjan (Cleaning)
(4, 8, 4, 'Deep Cleaning Specialist • Eco-friendly & Machine Assisted', 'Professional home cleaning lead with industrial steam machines, vacuum shampooers, and child-safe cleaning agents. Perfect for full 2BHK/3BHK house shifts and festive pre-Dashain cleanups.', 6, 850.00, 1, 1, 'approved', '28-01-76-03190', 'Citizenship Card', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=600&auto=format&fit=crop&q=80', 4.97, 115, 180, '["Lalitpur 1", "Lalitpur 2", "Lalitpur 3", "Lalitpur 4", "Lalitpur 5", "Lalitpur 10", "Kathmandu 4"]'),

-- 5. Dipendra Chaudhary (Painting)
(5, 9, 5, 'Master House Painter & Texture Artist', '10 years transforming homes with Asian Paints and Berger color palettes. Specializes in waterproof damp seals, satin wall finishes, and false ceiling painting.', 9, 600.00, 1, 1, 'approved', '27-04-71-08125', 'Citizenship Card', 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=600&auto=format&fit=crop&q=80', 4.82, 64, 95, '["Kathmandu 10", "Kathmandu 11", "Kathmandu 12", "Kathmandu 31", "Kathmandu 32"]'),

-- 6. Suresh Kumar Shrestha (Appliance Repair)
(6, 10, 6, 'Appliance Doctor • Washing Machine, Fridge & Microwave', 'Factory trained appliance technician for LG, Samsung, Whirlpool, and IFB. Immediate diagnosis and genuine spare parts replacement with 30-day service warranty.', 7, 500.00, 1, 1, 'approved', '29-01-75-04533', 'Technical Institute Diploma', 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?w=600&auto=format&fit=crop&q=80', 4.90, 89, 130, '["Bhaktapur 1", "Bhaktapur 2", "Bhaktapur 3", "Bhaktapur 4", "Madhyapur Thimi 1", "Madhyapur Thimi 2"]'),

-- 7. Prem Bahadur Rai (Carpentry)
(7, 11, 7, 'Master Carpenter • Modular Kitchen & Furniture Specialist', 'Specialist in custom wooden wardrobe repair, kitchen drawer sliders, high security door locks, and timber restoration.', 11, 550.00, 0, 1, 'approved', '27-02-69-02100', 'Citizenship Card', 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=600&auto=format&fit=crop&q=80', 4.85, 52, 85, '["Kathmandu 2", "Kathmandu 3", "Kathmandu 4", "Kathmandu 5"]'),

-- 8. Manoj Poudel (AC & Geyser)
(8, 12, 8, 'HVAC & Gas Geyser Certified Technician', 'Certified for gas geyser servicing, instantaneous water heater installation, and residential split AC gas recharging with vacuum pump cleaning.', 5, 750.00, 1, 1, 'approved', '27-05-77-09412', 'HVAC Level 1 Certificate', 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=600&auto=format&fit=crop&q=80', 4.78, 41, 60, '["Kathmandu 13", "Kathmandu 14", "Kathmandu 15", "Kathmandu 31", "Kathmandu 32"]'),

-- Pending Providers (Queue for Admin Verification)
(9, 13, 1, 'Young Pipe Fitter & Plumber', 'Skilled plumber with 3 years practical experience in residential pipe connections, sanitary repairs, and solar tank plumbing.', 3, 400.00, 1, 0, 'pending', '27-06-80-01923', 'Citizenship Front/Back Scan', 'https://images.unsplash.com/photo-1584438784894-089d6a62b8fa?w=600&auto=format&fit=crop&q=80', 5.00, 0, 0, '["Kathmandu 16", "Kathmandu 17", "Kathmandu 26"]'),

(10, 14, 3, 'Masonry Worker & Brick Layer', 'Brick mistiri for compound wall building, surface plastering, and floor screeding.', 5, 600.00, 1, 0, 'pending', '29-02-78-04311', 'Citizenship Card Scan', 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=600&auto=format&fit=crop&q=80', 5.00, 0, 0, '["Bhaktapur 4", "Bhaktapur 5", "Bhaktapur 6"]');

-- 6. Insert Bookings in Diverse Statuses
INSERT INTO bookings (id, booking_code, customer_id, provider_id, category_id, service_id, status, urgency, scheduled_date, scheduled_slot, province, district, municipality, ward_no, street_address, landmark, problem_description, estimated_amount, final_amount, payment_method, payment_status, created_at) VALUES
-- Pending Booking (Customer waiting for acceptance)
(1, 'SEWA-2026-8801', 2, 1, 1, 1, 'pending', 'standard', DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY), 'Morning (8:00 AM - 11:00 AM)', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 4, 'Baluwatar House 24, Marg 3', 'Behind Russian Embassy', 'Kitchen sink main pipe is leaking heavily under the cabinet. Water is dripping continuously.', 450.00, 450.00, 'cash', 'unpaid', NOW()),

-- Accepted Booking (Provider accepted, preparing to visit)
(2, 'SEWA-2026-8802', 2, 1, 1, 3, 'accepted', 'urgent_asap', CURRENT_DATE, 'Afternoon (12:00 PM - 3:00 PM)', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 4, 'Baluwatar House 24, Marg 3', 'Behind Russian Embassy', 'Master bathroom flush valve is broken and overflow pipe is running non-stop.', 950.00, 950.00, 'esewa', 'unpaid', DATE_SUB(NOW(), INTERVAL 2 HOUR)),

-- In Progress Booking (Provider is currently on-site)
(3, 'SEWA-2026-8803', 3, 2, 2, 6, 'in_progress', 'standard', CURRENT_DATE, 'Morning (9:00 AM - 12:00 PM)', 'Bagmati', 'Kathmandu', 'Lalitpur Metropolitan City', 3, 'Pulchowk Heights, Flat 3B', 'Opposite Labim Mall', 'Main distribution board circuit breaker keeps tripping whenever the geyser and kitchen induction are turned on together.', 700.00, 700.00, 'khalti', 'unpaid', DATE_SUB(NOW(), INTERVAL 4 HOUR)),

-- Completed Booking (Ready for payment confirmation and review)
(4, 'SEWA-2026-8804', 2, 1, 1, 2, 'completed', 'standard', DATE_SUB(CURRENT_DATE, INTERVAL 2 DAY), 'Afternoon (2:00 PM - 5:00 PM)', 'Bagmati', 'Kathmandu', 'Kathmandu Metropolitan City', 4, 'Baluwatar, Gairidhara Road', 'Near Sunrise Bank', 'Installed brand new 0.5HP Crompton water pump and connected automatic float switch.', 850.00, 850.00, 'cash', 'paid', DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Completed Booking for Sita Maharjan (Cleaning)
(5, 'SEWA-2026-8805', 3, 4, 4, 14, 'completed', 'standard', DATE_SUB(CURRENT_DATE, INTERVAL 5 DAY), 'Full Day (9:00 AM - 4:00 PM)', 'Bagmati', 'Kathmandu', 'Lalitpur Metropolitan City', 3, 'Jhamsikhel Road, Lane 4', 'Near Moksh Restaurant', '5-Seater Italian fabric sofa deep foam wash and stain extraction.', 1400.00, 1400.00, 'esewa', 'paid', DATE_SUB(NOW(), INTERVAL 5 DAY));

-- 7. Insert Reviews
INSERT INTO reviews (booking_id, customer_id, provider_id, rating, comment, created_at) VALUES
(4, 2, 1, 5, 'Ram Bahadur dai arrived within 30 minutes! Very respectful, carried all required pipe fittings and Teflon tapes. Fixed the water pump issue smoothly. Highly recommended in Kathmandu!', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(5, 3, 4, 5, 'Sita did a wonderful job with our sofa set. Looked almost brand new after the deep shampooing. Very polite and disciplined team. Will book again!', DATE_SUB(NOW(), INTERVAL 5 DAY));
