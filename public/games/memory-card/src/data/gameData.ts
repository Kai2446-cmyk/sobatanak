import type { Theme, LevelConfig, Level } from '../types/game';

export const themes: Record<string, Theme> = {
  animals: {
    id: 'animals',
    name: 'Binatang',
    gradient: 'linear-gradient(135deg, #FF9A8B 0%, #FF6B88 100%)',
    image: '/games/memory-card/dist/theme-animals.png',
    items: [
      { id: 'cat', name: 'Kucing', image: '/games/memory-card/dist/card/animals/cat.png' },
      { id: 'dog', name: 'Anjing', image: '/games/memory-card/dist/card/animals/dog.png' },
      { id: 'frog', name: 'Katak', image: '/games/memory-card/dist/card/animals/frog.png' },
      { id: 'lion', name: 'Singa', image: '/games/memory-card/dist/card/animals/lion.png' },
      { id: 'monkey', name: 'Monyet', image: '/games/memory-card/dist/card/animals/monkey.png' },
      { id: 'panda', name: 'Panda', image: '/games/memory-card/dist/card/animals/panda.png' },
      { id: 'pinguin', name: 'Pinguin', image: '/games/memory-card/dist/card/animals/pinguin.png' },
      { id: 'rabbit', name: 'Kelinci', image: '/games/memory-card/dist/card/animals/rabbit.png' },
      { id: 'tiger', name: 'Harimau', image: '/games/memory-card/dist/card/animals/tiger.png' },
    ],
  },
  fruits: {
    id: 'fruits',
    name: 'Buah',
    gradient: 'linear-gradient(135deg, #A8E6CF 0%, #88D8B0 100%)',
    image: '/games/memory-card/dist/theme_fruits.png',
    items: [
      { id: 'apple', name: 'Apel', image: '/games/memory-card/dist/card/fruits/apple.png' },
      { id: 'banana', name: 'Pisang', image: '/games/memory-card/dist/card/fruits/banana.png' },
      { id: 'cherry', name: 'Ceri', image: '/games/memory-card/dist/card/fruits/cherry.png' },
      { id: 'grapes', name: 'Anggur', image: '/games/memory-card/dist/card/fruits/grapes.png' },
      { id: 'mango', name: 'Mangga', image: '/games/memory-card/dist/card/fruits/mango.png' },
      { id: 'orange', name: 'Jeruk', image: '/games/memory-card/dist/card/fruits/orange.png' },
      { id: 'pineapple', name: 'Nanas', image: '/games/memory-card/dist/card/fruits/pineapple.png' },
      { id: 'strawberry', name: 'Stroberi', image: '/games/memory-card/dist/card/fruits/strawberry.png' },
      { id: 'watermelon', name: 'Semangka', image: '/games/memory-card/dist/card/fruits/watermelon.png' },
    ],
  },
  numbers: {
    id: 'numbers',
    name: 'Angka',
    gradient: 'linear-gradient(135deg, #FFD7A8 0%, #FFB38A 100%)',
    image: '/games/memory-card/dist/theme-numbers.png',
    items: [
      { id: '1', name: '', image: '/games/memory-card/dist/card/numbers/number-1.png' },
      { id: '2', name: '', image: '/games/memory-card/dist/card/numbers/number-2.png' },
      { id: '3', name: '', image: '/games/memory-card/dist/card/numbers/number-3.png' },
      { id: '4', name: '', image: '/games/memory-card/dist/card/numbers/number-4.png' },
      { id: '5', name: '', image: '/games/memory-card/dist/card/numbers/number-5.png' },
      { id: '6', name: '', image: '/games/memory-card/dist/card/numbers/number-6.png' },
      { id: '7', name: '', image: '/games/memory-card/dist/card/numbers/number-7.png' },
      { id: '8', name: '', image: '/games/memory-card/dist/card/numbers/number-8.png' },
      { id: '9', name: '', image: '/games/memory-card/dist/card/numbers/number-9.png' },
    ],
  },
  shapes: {
    id: 'shapes',
    name: 'Bentuk',
    gradient: 'linear-gradient(135deg, #FFAAA5 0%, #FF8B94 100%)',
    image: '/games/memory-card/dist/theme-shapes.png',
    items: [
      { id: 'circle', name: 'Lingkaran', image: '/games/memory-card/dist/card/shapes/circle.png' },
      { id: 'triangle', name: 'Segitiga', image: '/games/memory-card/dist/card/shapes/triangle.png' },
      { id: 'square', name: 'Persegi', image: '/games/memory-card/dist/card/shapes/square.png' },
      { id: 'star', name: 'Bintang', image: '/games/memory-card/dist/card/shapes/star.png' },
      { id: 'heart', name: 'Hati', image: '/games/memory-card/dist/card/shapes/heart.png' },
      { id: 'oval', name: 'Oval', image: '/games/memory-card/dist/card/shapes/oval.png' },
      { id: 'diamond', name: 'Permata', image: '/games/memory-card/dist/card/shapes/diamond.png' },
      { id: 'trapezoid', name: 'Trapesium', image: '/games/memory-card/dist/card/shapes/trapezoid.png' },
      { id: 'pentagon', name: 'Segi Lima', image: '/games/memory-card/dist/card/shapes/pentagon.png' },
    ],
  },
};

export const levelConfigs: Record<Level, LevelConfig> = {
  easy: {
    name: 'Mudah',
    cardCount: 8,
    gridCols: 4,
    gridRows: 2,
  },
  medium: {
    name: 'Sedang',
    cardCount: 12,
    gridCols: 4,
    gridRows: 3,
  },
  hard: {
    name: 'Sulit',
    cardCount: 18,
    gridCols: 6,
    gridRows: 3,
  },
};

export const floatingEmojis = ['🐱', '🍎', '⭐', '🐰', '🎈', '🌈', '🦋', '🌸', '🎮', '🎨', '🎪', '🎠'];
