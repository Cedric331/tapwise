# Assets pour la page Marketing

Les assets suivants doivent être copiés vers `public/images/` pour être accessibles :

- `illustration-bartender.png` → `public/images/illustration-bartender.png`
- `illustration-beer-glass.png` → `public/images/illustration-beer-glass.png`
- `illustration-qr-frame.png` → `public/images/illustration-qr-frame.png`
- `hero-video.mp4` → `public/images/hero-video.mp4` (optionnel)

## Commande pour copier les assets

```bash
mkdir -p public/images
cp resources/assets/*.png public/images/
cp resources/assets/*.mp4 public/images/
```

Ou sous Windows PowerShell :
```powershell
New-Item -ItemType Directory -Force -Path public/images
Copy-Item resources/assets/*.png public/images/
Copy-Item resources/assets/*.mp4 public/images/
```

