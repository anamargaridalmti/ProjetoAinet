<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>FunShirt - Catálogo de T-Shirts</title>
    <style>
        body { background-color: #121212; color: white; font-family: sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #333; margin-bottom: 30px; }
        .filter-bar { background-color: #1e1e1e; padding: 15px; border-radius: 8px; margin-bottom: 30px; display: flex; gap: 15px; align-items: center; }
        .filter-bar input, .filter-bar select, .filter-bar button { padding: 10px; background-color: #2a2a2a; color: white; border: 1px solid #333; border-radius: 4px; }
        .filter-bar button { background-color: #ff4757; border: none; font-weight: bold; cursor: pointer; }
        .filter-bar button:hover { background-color: #e84118; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px; }
        .card { background-color: #1e1e1e; border: 1px solid #333; border-radius: 8px; padding: 15px; text-align: center; display: flex; flex-direction: column; justify-content: space-between; }
        .card img { max-width: 100%; height: 200px; object-fit: contain; background-color: #fff; border-radius: 6px; padding: 10px; }
        .card h3 { margin: 15px 0 5px 0; font-size: 18px; }
        .card p { color: #888; font-size: 14px; margin: 0 0 15px 0; height: 40px; overflow: hidden; }
        .badge { background-color: #333; color: #aaa; padding: 4px 8px; border-radius: 12px; font-size: 12px; display: inline-block; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="navbar">
        <h2>👕 Loja FunShirt</h2>
        <div>
            @auth
                <a href="/profile" style="color: #ff4757; text-decoration: none; font-weight: bold;">👤 O Meu Perfil</a>
            @else
                <a href="/login" style="color: #aaa; text-decoration: none; margin-right: 15px;">Login</a>
                <a href="/register" style="color: #ff4757; text-decoration: none; font-weight: bold;">Registar</a>
            @endauth
        </div>
    </div>

    <form method="GET" action="/catalog" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou descrição...">
        
        <select name="category_id">
            <option value="">Todas as Categorias</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>

        <button type="submit">Filtrar</button>
        <a href="/catalog" style="color: #aaa; text-decoration: none; font-size: 14px;">Limpar</a>
    </form>

    <div class="grid">
        @forelse($tshirts as $tshirt)
            <div class="card">
                <div>
                    <img src="{{ $tshirt->image_url ? asset('images/catalog/' . $tshirt->image_url) : asset('storage/tshirt_base/white.png') }}" alt="{{ $tshirt->name }}">
                    <h3>{{ $tshirt->name }}</h3>
                    
                    @if($tshirt->category)
                        <span class="badge">📁 {{ $tshirt->category->name }}</span>
                    @else
                        <span class="badge">Sem categoria</span>
                    @endif
                    
                    <p>{{ $tshirt->description ?? 'Sem descrição disponível.' }}</p>
                </div>
            </div>
        @empty
            <p style="grid-column: 1/-1; text-align: center; color: #888;">Nenhuma t-shirt encontrada no catálogo.</p>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $tshirts->links() }}
    </div>
</div>

</body>
</html>