<h1>Galería de especies marinas</h1>

<?php
// Obtener filtros
$category_filter = $_GET['category'] ?? '';
$species_filter = $_GET['species'] ?? '';

$gallery_items = [
    ['img' => 'https://images.unsplash.com/photo-1561553580-b27900e8edb5?w=400', 
     'name' => 'Pez Payaso', 
     'scientific' => 'Amphiprioninae',
     'category' => 'peces',
     'species' => 'pez_payaso'],
    ['img' => 'https://images.unsplash.com/photo-1560275619-4662e36fa65c?w=400',
     'name' => 'Delfín Mular',
     'scientific' => 'Tursiops truncatus',
     'category' => 'mamiferos',
     'species' => 'delfin'],
    ['img' => 'https://images.unsplash.com/photo-1568430462989-44163eb1752f?w=400',
     'name' => 'Ballena Jorobada',
     'scientific' => 'Megaptera novaeangliae',
     'category' => 'mamiferos',
     'species' => 'ballena'],
    ['img' => 'https://images.unsplash.com/photo-1524704796725-9fc3044a58b2?w=400',
     'name' => 'Pez Ángel',
     'scientific' => 'Pomacanthidae',
     'category' => 'peces',
     'species' => 'pez_angel'],
    ['img' => 'https://images.unsplash.com/photo-1582967788606-a171d1080cb0?w=400',
     'name' => 'Coral Cerebro',
     'scientific' => 'Diploria labyrinthiformis',
     'category' => 'corales',
     'species' => 'coral'],
    ['img' => 'https://images.unsplash.com/photo-1559586619-99fbb9a8e1d8?w=400',
     'name' => 'Pulpo Común',
     'scientific' => 'Octopus vulgaris',
     'category' => 'moluscos',
     'species' => 'pulpo']
];
?>

<div class="gallery-filters">
    <form method="GET" class="gallery-filter-form">
        <input type="hidden" name="section" value="galeria">
        <select name="category" class="gallery-select" onchange="this.form.submit()">
            <option value="">Todas las categorías</option>
            <option value="peces" <?php echo $category_filter == 'peces' ? 'selected' : ''; ?>>Peces</option>
            <option value="mamiferos" <?php echo $category_filter == 'mamiferos' ? 'selected' : ''; ?>>Mamíferos</option>
            <option value="moluscos" <?php echo $category_filter == 'moluscos' ? 'selected' : ''; ?>>Moluscos</option>
            <option value="crustaceos" <?php echo $category_filter == 'crustaceos' ? 'selected' : ''; ?>>Crustáceos</option>
            <option value="corales" <?php echo $category_filter == 'corales' ? 'selected' : ''; ?>>Corales</option>
        </select>
        
        <select name="species" class="gallery-select" onchange="this.form.submit()">
            <option value="">Todas las especies</option>
            <optgroup label="Peces">
                <option value="pez_payaso" <?php echo $species_filter == 'pez_payaso' ? 'selected' : ''; ?>>Pez Payaso</option>
                <option value="pez_angel" <?php echo $species_filter == 'pez_angel' ? 'selected' : ''; ?>>Pez Ángel</option>
            </optgroup>
            <optgroup label="Mamíferos">
                <option value="delfin" <?php echo $species_filter == 'delfin' ? 'selected' : ''; ?>>Delfín</option>
                <option value="ballena" <?php echo $species_filter == 'ballena' ? 'selected' : ''; ?>>Ballena</option>
            </optgroup>
        </select>
    </form>
</div>

<div class="gallery-grid">
    <?php foreach ($gallery_items as $item): ?>
        <?php
        if ($category_filter && $item['category'] !== $category_filter) continue;
        if ($species_filter && $item['species'] !== $species_filter) continue;
        ?>
        <div class="gallery-item">
            <img src="<?php echo $item['img']; ?>" alt="<?php echo $item['name']; ?>">
            <div class="gallery-info">
                <h3><?php echo $item['name']; ?></h3>
                <p><?php echo $item['scientific']; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>