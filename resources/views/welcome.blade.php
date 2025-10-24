<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Cozy Taste Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans text-gray-800 bg-amber-50">

    <!-- Hero Section -->
    <section
        class="relative h-screen bg-[url('https://images.unsplash.com/photo-1600891964599-f61ba0e24092')] bg-cover bg-center flex items-center justify-center">
        <div class="bg-black/50 absolute inset-0"></div>
        <div class="relative z-10 text-center text-white max-w-2xl">
            <h1 class="text-5xl font-bold mb-4">Cozy Taste Restaurant</h1>
            <p class="text-lg mb-6">อาหารรสชาติดี • บรรยากาศอบอุ่น • ใส่ใจทุกจาน</p>
            <a href="#menu"
                class="px-6 py-3 bg-amber-500 hover:bg-amber-600 rounded-full font-semibold">ดูเมนูของเรา</a>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16 px-6 text-center">
        <h2 class="text-3xl font-bold mb-4">เกี่ยวกับร้านของเรา</h2>
        <p class="max-w-3xl mx-auto text-gray-600">
            ร้าน Cozy Taste เปิดให้บริการด้วยใจรักอาหารและความสุขของลูกค้า
            เราคัดสรรวัตถุดิบสดใหม่ทุกวัน พร้อมบริการในบรรยากาศอบอุ่นสไตล์โฮมคาเฟ่
        </p>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="bg-white py-16 px-6">
        <h2 class="text-3xl font-bold text-center mb-10">เมนูยอดนิยม</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="bg-amber-100 rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition">
                <img src="https://images.unsplash.com/photo-1551218808-94e220e084d2" alt="สเต็กเนื้อ"
                    class="w-full h-56 object-cover">
                <div class="p-5">
                    <h3 class="font-semibold text-xl mb-2">สเต็กเนื้อพรีเมียม</h3>
                    <p class="text-gray-600 text-sm">เสิร์ฟพร้อมซอสไวน์แดงและมันบด</p>
                </div>
            </div>
            <div class="bg-amber-100 rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition">
                <img src="https://images.unsplash.com/photo-1586190848861-99aa4a171e90" alt="สปาเกตตี้"
                    class="w-full h-56 object-cover">
                <div class="p-5">
                    <h3 class="font-semibold text-xl mb-2">สปาเกตตี้คาโบนารา</h3>
                    <p class="text-gray-600 text-sm">เข้มข้นด้วยชีสและเบคอนกรอบ</p>
                </div>
            </div>
            <div class="bg-amber-100 rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition">
                <img src="https://images.unsplash.com/photo-1571091718767-18b5b1457add" alt="สลัดผัก"
                    class="w-full h-56 object-cover">
                <div class="p-5">
                    <h3 class="font-semibold text-xl mb-2">สลัดผักโฮมเมด</h3>
                    <p class="text-gray-600 text-sm">สดใหม่ทุกวัน พร้อมน้ำสลัดสูตรเฉพาะ</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="bg-green-50 py-16 text-center px-6">
        <h2 class="text-3xl font-bold mb-8">เสียงจากลูกค้าของเรา</h2>
        <div class="max-w-4xl mx-auto grid md:grid-cols-3 gap-8">
            <div class="p-6 bg-white rounded-xl shadow-md">
                <p class="text-gray-600 mb-4">“อาหารอร่อย บรรยากาศดีมาก เหมาะกับมื้อพิเศษสุด ๆ”</p>
                <span class="font-semibold">– คุณเมย์</span>
            </div>
            <div class="p-6 bg-white rounded-xl shadow-md">
                <p class="text-gray-600 mb-4">“บริการประทับใจตั้งแต่เข้าร้านจนออกจากร้านเลยค่ะ”</p>
                <span class="font-semibold">– คุณกิตติ</span>
            </div>
            <div class="p-6 bg-white rounded-xl shadow-md">
                <p class="text-gray-600 mb-4">“ร้านสวย สะอาด และอาหารสดมากครับ ชอบมาก”</p>
                <span class="font-semibold">– คุณต่อ</span>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 bg-amber-500 text-center text-white">
        <h2 class="text-3xl font-bold mb-4">พร้อมจองโต๊ะแล้วหรือยัง?</h2>
        <p class="mb-6">สำรองที่นั่งล่วงหน้าเพื่อไม่พลาดมื้อพิเศษของคุณ</p>
        <a href="#!"
            class="bg-white text-amber-600 px-8 py-3 rounded-full font-semibold hover:bg-amber-100">จองโต๊ะเลย</a>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-6 text-center">
        <p>© 2025 Cozy Taste Restaurant. All Rights Reserved.</p>
    </footer>

</body>

</html>
