import type { Level, ThemeName } from '../types/game';

interface LoseModalProps {
  level: Level;
  themeName: ThemeName;
  score: number;
  moves: number;
  onBack: () => void;
  onRestart: () => void;
}

export function LoseModal({ themeName, score, moves, onBack, onRestart }: LoseModalProps) {
  const isSolisTheme = themeName === 'animals' || themeName === 'fruits';
  const characterImage = isSolisTheme ? '/games/memory-card/dist/wl-modals/solis-lose.png' : '/games/memory-card/dist/wl-modals/selena-lose.png';
  const bgColor = isSolisTheme ? '#FEF3C7' : '#E6E6FA';
  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div className="rounded-3xl shadow-2xl p-4 md:p-6 max-w-2xl w-full animate-pop-in border-4 border-white/50 relative overflow-hidden" style={{ backgroundColor: bgColor }}>
        {/* Decoration */}
        <div className="absolute -top-4 left-1/4 text-3xl animate-pulse">💔</div>
        <div className="absolute -top-4 right-1/4 text-3xl animate-pulse delay-500">❌</div>

        {/* Main content */}
        <div className="text-center relative z-10">
          {/* Logo Sobat Anak */}
          <div className="w-12 h-12 md:w-16 md:h-16 rounded-full overflow-hidden shadow-xl flex items-center justify-center bg-white p-1 border-2 border-white/50 mx-auto mb-2">
            <img 
              src="/games/memory-card/dist/logo-sobat-anak.png" 
              alt="Logo Sobat Anak" 
              className="w-full h-full object-contain"
            />
          </div>
          {/* Character Image */}
          <div className="w-48 h-32 md:w-64 md:h-40 mx-auto mb-2">
            <img 
              src={characterImage} 
              alt="Character" 
              className="w-full h-full object-contain"
            />
          </div>
          <h2 className="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            Waktu Habis!
          </h2>
          <p className="text-gray-700 text-lg md:text-xl mb-5">
            Jangan menyerah, coba lagi!
          </p>

          {/* Stats */}
          <div className="bg-white/50 rounded-2xl p-4 md:p-5 mb-5">
            <div className="grid grid-cols-2 gap-3 md:gap-4">
              <div className="bg-white/70 rounded-xl p-3">
                <p className="text-gray-600 text-xs uppercase">Skor</p>
                <p className="text-2xl md:text-3xl font-bold text-gray-800">{score}</p>
              </div>
              <div className="bg-white/70 rounded-xl p-3">
                <p className="text-gray-600 text-xs uppercase">Langkah</p>
                <p className="text-2xl md:text-3xl font-bold text-gray-800">{moves}</p>
              </div>
            </div>
          </div>

          {/* Encouraging message */}
          <div className="bg-white/30 rounded-xl p-3 mb-5">
            <p className="text-gray-800 text-sm md:text-base">
              🌟 Kamu hampir berhasil! Ayo coba sekali lagi!
            </p>
          </div>

          {/* Buttons */}
          <div className="flex gap-3">
            <button
              onClick={onBack}
              className="flex-1 py-3 px-4 bg-white/50 text-gray-800 rounded-xl font-bold hover:bg-white/70 transition-colors border-2 border-white/50 text-sm md:text-base"
            >
              Kembali
            </button>
            <button
              onClick={onRestart}
              className="flex-1 py-3 px-4 bg-white text-red-600 rounded-xl font-bold hover:bg-white/90 transition-colors shadow-lg text-sm md:text-base"
            >
              Ulangi!
            </button>
          </div>
        </div>
      </div>

      <style>{`
        @keyframes pop-in {
          0% { transform: scale(0.8); opacity: 0; }
          100% { transform: scale(1); opacity: 1; }
        }
        .animate-pop-in { animation: pop-in 0.4s ease-out; }
        .delay-500 { animation-delay: 0.5s; }
      `}</style>
    </div>
  );
}
