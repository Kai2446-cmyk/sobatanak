import React, { useEffect, useMemo, useState } from 'react';
import { getPuzzlesByDifficulty, DIFFICULTY_CONFIG } from '../constants';
import { PuzzleData, DifficultyLevel } from '../types';

interface LevelSelectorProps {
  difficulty: DifficultyLevel;
  onSelect: (puzzle: PuzzleData) => void;
  onBack: () => void;
}

const LevelSelector: React.FC<LevelSelectorProps> = ({
  difficulty,
  onSelect,
  onBack,
}) => {
  const [mounted, setMounted] = useState(false);
  const [pendingPuzzle, setPendingPuzzle] = useState<PuzzleData | null>(null);

  useEffect(() => {
    setMounted(true);

    const desktopMq = window.matchMedia('(min-width: 1024px)');

    const updateBodyScroll = () => {
      document.body.style.overflow = desktopMq.matches ? 'hidden' : '';
    };

    updateBodyScroll();
    desktopMq.addEventListener('change', updateBodyScroll);

    return () => {
      desktopMq.removeEventListener('change', updateBodyScroll);
      document.body.style.overflow = '';
    };
  }, []);

  const getDifficultyConfig = (diff: DifficultyLevel) => {
    switch (diff) {
      case 'mudah':
        return {
          primary: '#81C784',
          light: '#ECFDF5',
          styles: 'bg-[#ECFDF5] text-[#81C784] border-[#81C784]',
        };

      case 'sedang':
        return {
          primary: '#F97316',
          light: '#FFF7ED',
          styles: 'bg-[#FFF7ED] text-[#F97316] border-[#F97316]',
        };

      case 'sulit':
        return {
          primary: '#EF4444',
          light: '#FEF2F2',
          styles: 'bg-[#FEF2F2] text-[#EF4444] border-[#EF4444]',
        };

      default:
        return {
          primary: '#94A3B8',
          light: '#F1F5F9',
          styles: 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }
  };

  const puzzles = useMemo(
    () => getPuzzlesByDifficulty(difficulty),
    [difficulty],
  );

  const difficultyConfig = DIFFICULTY_CONFIG[difficulty];
  const config = getDifficultyConfig(difficulty);

  const difficultyLabel =
    difficulty === 'mudah'
      ? 'Mudah'
      : difficulty === 'sedang'
        ? 'Sedang'
        : 'Sulit';

  const handleCardClick = (puzzle: PuzzleData) => {
    setPendingPuzzle(puzzle);
  };

  const confirmSelection = () => {
    if (!pendingPuzzle) return;
    onSelect(pendingPuzzle);
  };

  const cancelSelection = () => {
    setPendingPuzzle(null);
  };

  return (
    <div className="relative min-h-screen overflow-x-hidden overflow-y-auto bg-[#FFF7ED] px-4 py-10 sm:px-6 md:py-12 lg:flex lg:h-screen lg:max-h-screen lg:flex-col lg:overflow-hidden lg:py-6">
      {/* Background */}
      <div className="pointer-events-none fixed inset-0 -z-20 bg-gradient-to-br from-[#ECFDF5] via-white to-[#EFF6FF]" />

      <div
        className="pointer-events-none fixed inset-0 -z-10 opacity-[0.03]"
        style={{
          backgroundImage:
            'radial-gradient(circle at 1px 1px, #81C784 1px, transparent 0)',
          backgroundSize: '24px 24px',
        }}
      />

      {/* Dekorasi statis */}
      <div className="pointer-events-none absolute left-[5%] top-[10%] select-none text-7xl opacity-20">
        🍃
      </div>

      <div className="pointer-events-none absolute bottom-[10%] right-[5%] select-none text-8xl opacity-20">
        🌿
      </div>

      <div className="pointer-events-none absolute right-[10%] top-[30%] select-none text-5xl opacity-30">
        🦋
      </div>

      <div className="pointer-events-none absolute bottom-[30%] left-[8%] select-none text-6xl opacity-10">
        🌸
      </div>

      {/* Solis desktop */}
      <div
        className={`
          pointer-events-none fixed bottom-0 left-3 z-10 hidden
          w-[230px] xl:block 2xl:left-8 2xl:w-[280px]
          transition-opacity duration-700
          ${mounted ? 'opacity-100' : 'opacity-0'}
        `}
      >
        <div className="relative overflow-visible">
          <div className="absolute bottom-6 left-1/2 h-10 w-[65%] -translate-x-1/2 rounded-full bg-[#F9C642]/25" />

          <img
            src="/games/puzzle/dist/assets/karakter/solis-melambai.png"
            alt="Solis melambai"
            draggable={false}
            className="relative z-10 h-auto w-full object-contain drop-shadow-[0_18px_20px_rgba(44,78,144,0.18)] animate-character-float"
          />

          <div className="absolute bottom-full left-1/2 z-20 mb-3 -translate-x-1/2">
            <div className="relative w-[190px] rounded-[1.5rem] border-2 border-[#F7C84A]/50 bg-white px-5 py-3.5 shadow-[0_12px_30px_rgba(61,76,145,0.20)]">
              <p className="text-center font-fredoka text-sm leading-snug text-[#3C5A9A]">
                Semua gambar punya tantangan yang seru!
              </p>

              <span className="absolute -bottom-2 left-1/2 h-4 w-4 -translate-x-1/2 rotate-45 border-b-2 border-r-2 border-[#F7C84A]/50 bg-white" />
            </div>
          </div>
        </div>
      </div>

      {/* Selena desktop */}
      <div
        className={`
          pointer-events-none fixed bottom-0 right-3 z-10 hidden
          w-[230px] xl:block 2xl:right-8 2xl:w-[280px]
          transition-opacity duration-700 delay-150
          ${mounted ? 'opacity-100' : 'opacity-0'}
        `}
      >
        <div className="relative overflow-visible">
          <div className="absolute bottom-6 left-1/2 h-10 w-[65%] -translate-x-1/2 rounded-full bg-[#8E7AE6]/25" />

          <img
            src="/games/puzzle/dist/assets/karakter/selena-menunjuk.png"
            alt="Selena menunjuk pilihan gambar"
            draggable={false}
            className="relative z-10 h-auto w-full object-contain drop-shadow-[0_18px_20px_rgba(83,73,151,0.18)] animate-character-float-delayed"
          />

          <div className="absolute bottom-full left-1/2 z-20 mb-3 -translate-x-1/2">
            <div className="relative w-[200px] rounded-[1.5rem] border-2 border-[#A594E9]/50 bg-white px-5 py-3.5 shadow-[0_12px_30px_rgba(61,76,145,0.20)]">
              <p className="text-center font-fredoka text-sm leading-snug text-[#6757B6]">
                Pilih gambar yang ingin kamu susun.
              </p>

              <span className="absolute -bottom-2 left-1/2 h-4 w-4 -translate-x-1/2 rotate-45 border-b-2 border-r-2 border-[#A594E9]/50 bg-white" />
            </div>
          </div>
        </div>
      </div>

      {/* Konten utama */}
      <div className="relative z-20 mx-auto flex w-full max-w-6xl flex-1 flex-col lg:min-h-0 lg:justify-center lg:px-2 lg:pb-14 xl:max-w-none xl:px-[250px] 2xl:px-[300px]">
        {/* Header */}
        <div
          className={`
            mb-8 flex flex-col items-center
            transition-opacity duration-700
            md:mb-10 md:flex-row
            lg:mb-6
            ${mounted ? 'opacity-100' : 'opacity-0'}
          `}
        >
          <button
            type="button"
            onClick={onBack}
            aria-label="Kembali ke pilihan tingkat kesulitan"
            className="group mb-6 flex items-center justify-center rounded-[2rem] border-b-8 border-[#ECFDF5] bg-white p-4 text-[#81C784] shadow-lg transition-all hover:translate-y-1 hover:border-b-4 active:translate-y-2 active:border-b-0 md:mb-0"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-7 w-7 transition-transform group-hover:-translate-x-1"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={3}
                d="M10 19l-7-7m0 0l7-7m-7 7h18"
              />
            </svg>
          </button>

          <div className="flex-1 text-center">
            <h1 className="bg-gradient-to-r from-[#81C784] via-[#81C784] to-[#4FC3F7] bg-clip-text font-fredoka text-5xl text-transparent drop-shadow-sm md:text-6xl lg:text-7xl">
              Pilih Gambar
            </h1>

            <p className="mt-2 text-xs font-bold uppercase tracking-[0.25em] text-[#1E2939]/40 sm:text-sm lg:tracking-[0.2em]">
              Tentukan pilihanmu!
            </p>
          </div>

          <div className="hidden w-16 md:block" />
        </div>

        {/* Karakter mobile dan tablet */}
        <div
          className={`
            mb-8 grid grid-cols-2 gap-4 xl:hidden
            transition-opacity duration-700 delay-100
            ${mounted ? 'opacity-100' : 'opacity-0'}
          `}
        >
          <div className="relative flex min-h-[185px] items-end justify-center overflow-visible rounded-[2rem] border border-white/80 bg-white/65 px-3 pt-3 shadow-md">
            <div className="absolute inset-x-0 bottom-0 h-16 rounded-b-[2rem] bg-gradient-to-t from-[#F8D567]/20 to-transparent" />

            <div className="relative">
              <div className="absolute bottom-full left-1/2 z-30 mb-2 max-w-[130px] -translate-x-1/2 rounded-xl border border-[#F7C84A]/30 bg-white px-3 py-2 shadow-sm">
                <p className="text-center text-[10px] font-bold leading-tight text-[#4767A7] sm:text-xs">
                  Semua gambar seru!
                </p>
              </div>

              <img
                src="/games/puzzle/dist/assets/karakter/solis-melambai.png"
                alt="Solis melambai"
                draggable={false}
                className="relative z-10 h-[145px] w-auto object-contain sm:h-[180px]"
              />
            </div>
          </div>

          <div className="relative flex min-h-[185px] items-end justify-center overflow-visible rounded-[2rem] border border-white/80 bg-white/65 px-3 pt-3 shadow-md">
            <div className="absolute inset-x-0 bottom-0 h-16 rounded-b-[2rem] bg-gradient-to-t from-[#9482E7]/20 to-transparent" />

            <div className="relative">
              <div className="absolute bottom-full left-1/2 z-30 mb-2 max-w-[145px] -translate-x-1/2 rounded-xl border border-[#A594E9]/30 bg-white px-3 py-2 shadow-sm">
                <p className="text-center text-[10px] font-bold leading-tight text-[#6A5ABB] sm:text-xs">
                  Pilih yang kamu suka.
                </p>
              </div>

              <img
                src="/games/puzzle/dist/assets/karakter/selena-menunjuk.png"
                alt="Selena menunjuk pilihan gambar"
                draggable={false}
                className="relative z-10 h-[145px] w-auto object-contain sm:h-[180px]"
              />
            </div>
          </div>
        </div>

        {/* Grid gambar */}
        <div className="relative mx-auto grid w-full max-w-6xl grid-cols-1 gap-8 md:grid-cols-3 lg:gap-5 xl:max-w-[700px] xl:gap-4 2xl:max-w-[780px]">
          {puzzles.map((puzzle, index) => (
            <button
              type="button"
              key={puzzle.id}
              onClick={() => handleCardClick(puzzle)}
              className={`
                group relative cursor-pointer
                rounded-[3.5rem]
                border-4 bg-white p-5
                text-left
                shadow-[0_20px_50px_-15px_rgba(0,0,0,0.1)]
                transition-[transform,opacity,box-shadow,border-color]
                duration-500
                hover:-translate-y-3
                hover:shadow-[0_25px_55px_-15px_rgba(0,0,0,0.16)]
                focus:outline-none focus-visible:ring-4
                focus-visible:ring-offset-4
                lg:rounded-[2.5rem] lg:p-4
                xl:rounded-[2rem] xl:p-3
                ${mounted ? 'opacity-100' : 'opacity-0'}
              `}
              style={
                {
                  transitionDelay: `${index * 100}ms`,
                  '--primary-color': config.primary,
                  borderColor: 'white',
                } as React.CSSProperties
              }
              onMouseEnter={(event) => {
                event.currentTarget.style.borderColor = config.primary;

                const overlay = event.currentTarget.querySelector(
                  '.overlay-color',
                ) as HTMLElement | null;

                if (overlay) {
                  overlay.style.backgroundColor = `${config.primary}20`;
                }

                const ring = event.currentTarget.querySelector(
                  '.img-ring',
                ) as HTMLElement | null;

                if (ring) {
                  ring.style.boxShadow = `0 0 0 7px ${config.light}80`;
                }
              }}
              onMouseLeave={(event) => {
                event.currentTarget.style.borderColor = 'white';

                const overlay = event.currentTarget.querySelector(
                  '.overlay-color',
                ) as HTMLElement | null;

                if (overlay) {
                  overlay.style.backgroundColor = 'transparent';
                }

                const ring = event.currentTarget.querySelector(
                  '.img-ring',
                ) as HTMLElement | null;

                if (ring) {
                  ring.style.boxShadow = '';
                }
              }}
            >
              {/* Gambar */}
              <div className="img-ring relative mb-6 aspect-[4/3] overflow-hidden rounded-[2.5rem] shadow-inner transition-shadow duration-300 lg:mb-4 lg:rounded-[2rem] xl:mb-3 xl:aspect-[5/4] xl:rounded-[1.5rem]">
                <img
                  src={puzzle.image}
                  alt={puzzle.title}
                  draggable={false}
                  className="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                />

                <div
                  className={`
                    absolute right-5 top-5 rounded-2xl
                    border-2 px-5 py-2
                    text-sm font-black uppercase tracking-widest
                    shadow-lg backdrop-blur-md
                    lg:right-3 lg:top-3 lg:px-4 lg:py-1.5 lg:text-xs
                    xl:right-2 xl:top-2 xl:px-3 xl:py-1 xl:text-[10px]
                    ${config.styles}
                  `}
                >
                  {difficultyLabel}
                </div>

                <div className="overlay-color absolute inset-0 flex items-center justify-center opacity-0 transition-opacity group-hover:opacity-100">
                  <div className="scale-50 rounded-full bg-white/90 p-6 shadow-2xl transition-transform duration-500 group-hover:scale-100 lg:p-5 xl:p-4">
                    <svg
                      className="h-10 w-10 lg:h-8 lg:w-8 xl:h-7 xl:w-7"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                      style={{ color: config.primary }}
                    >
                      <path d="M8 5v14l11-7z" />
                    </svg>
                  </div>
                </div>
              </div>

              {/* Informasi */}
              <div className="px-4 pb-4 lg:px-2 lg:pb-2 xl:px-1 xl:pb-1">
                <h3 className="mb-3 font-fredoka text-3xl text-[#1E2939] transition-colors group-hover:[color:var(--primary-color)] lg:mb-2 lg:text-2xl xl:mb-1.5 xl:text-xl">
                  {puzzle.title}
                </h3>

                <div
                  className="flex items-center justify-between rounded-3xl border p-4 lg:rounded-2xl lg:p-3 xl:rounded-xl xl:p-2.5"
                  style={{
                    backgroundColor: `${config.light}80`,
                    borderColor: config.light,
                  }}
                >
                  <div className="flex flex-col">
                    <span
                      className="text-[10px] font-black uppercase tracking-widest xl:text-[9px]"
                      style={{ color: config.primary }}
                    >
                      Tingkat Kesulitan
                    </span>

                    <span className="font-fredoka text-xl text-[#1E2939] lg:text-lg xl:text-base">
                      {difficultyConfig.pieces} Keping
                    </span>
                  </div>

                  <div
                    className="flex h-12 w-12 items-center justify-center rounded-2xl text-white shadow-lg transition-transform group-hover:scale-105 lg:h-10 lg:w-10 lg:rounded-xl xl:h-9 xl:w-9 xl:rounded-lg"
                    style={{ backgroundColor: config.primary }}
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      className="h-6 w-6 lg:h-5 lg:w-5 xl:h-4 xl:w-4"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={3}
                        d="M12 4v16m8-8H4"
                      />
                    </svg>
                  </div>
                </div>
              </div>
            </button>
          ))}
        </div>

        {/* Footer */}
        <div
          className={`
            mt-16 text-center
            transition-opacity duration-1000 delay-700
            lg:fixed lg:bottom-5 lg:left-0 lg:right-0 lg:mt-0 lg:px-6
            ${mounted ? 'opacity-50 lg:opacity-60' : 'opacity-0'}
          `}
        >
          <p className="flex items-center justify-center gap-2 text-sm font-bold text-[#1E2939]/70 lg:gap-3 lg:text-xs lg:uppercase lg:tracking-[0.18em]">
            <span
              className="hidden h-px w-6 sm:block lg:w-10"
              style={{ backgroundColor: `${config.primary}99` }}
            />

            Pilih satu gambar untuk mulai menyusun puzzle

            <span
              className="hidden h-px w-6 sm:block lg:w-10"
              style={{ backgroundColor: `${config.primary}99` }}
            />
          </p>
        </div>
      </div>

      {/* Modal konfirmasi */}
      {pendingPuzzle && (
        <div className="fixed inset-0 z-[1000] flex items-center justify-center bg-[#1E2939]/70 p-4 backdrop-blur-xl animate-fade-in sm:p-6">
          <div
            className="w-full max-w-xl rounded-[3rem] bg-white p-7 text-center shadow-[0_40px_100px_-20px_rgba(0,0,0,0.6)] animate-pop sm:rounded-[4rem] sm:p-12 lg:max-w-lg lg:p-8"
            style={{
              borderWidth: '10px',
              borderStyle: 'solid',
              borderColor: config.primary,
            }}
          >
            <div className="relative mx-auto mb-6 w-28 sm:mb-8 sm:w-36 lg:mb-5 lg:w-24">
              <img
                src="/games/puzzle/dist/assets/ekspresi/selena-berpikir.png"
                alt="Selena sedang berpikir"
                draggable={false}
                className="relative z-10 h-auto w-full object-contain"
              />
            </div>

            <h2 className="mb-4 font-fredoka text-3xl leading-tight text-[#1E2939] sm:text-4xl md:text-5xl lg:text-4xl">
              Apakah kamu yakin ingin memilih gambar ini?
            </h2>

            <div
              className="mb-8 flex items-center gap-4 rounded-[2.5rem] border-2 border-dashed p-4 sm:mb-12 sm:gap-6 sm:p-6 lg:mb-7 lg:rounded-[2rem] lg:p-4"
              style={{
                backgroundColor: config.light,
                borderColor: config.primary,
              }}
            >
              <div className="h-20 w-20 flex-shrink-0 overflow-hidden rounded-3xl shadow-lg sm:h-24 sm:w-24 lg:h-20 lg:w-20 lg:rounded-2xl">
                <img
                  src={pendingPuzzle.image}
                  className="h-full w-full object-cover"
                  alt={`Pratinjau ${pendingPuzzle.title}`}
                />
              </div>

              <div className="text-left">
                <p className="font-fredoka text-xl text-[#1E2939] sm:text-2xl">
                  {pendingPuzzle.title}
                </p>

                <p
                  className="text-xs font-bold uppercase tracking-widest sm:text-sm"
                  style={{ color: config.primary }}
                >
                  {difficultyLabel} •{' '}
                  {pendingPuzzle.rows * pendingPuzzle.cols} Keping
                </p>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4 sm:gap-6">
              <button
                type="button"
                onClick={cancelSelection}
                className="rounded-[2rem] bg-slate-100 py-5 font-fredoka text-xl text-slate-500 shadow-[0_8px_0_0_#CBD5E1] transition-all hover:translate-y-[4px] hover:bg-slate-200 hover:shadow-[0_4px_0_0_#CBD5E1] active:translate-y-[8px] active:shadow-none sm:py-6 sm:text-3xl lg:py-4 lg:text-2xl"
              >
                BATAL
              </button>

              <button
                type="button"
                onClick={confirmSelection}
                className="group flex items-center justify-center gap-3 rounded-[2rem] py-5 font-fredoka text-xl text-white shadow-[0_8px_0_0_#4A7B52] transition-all hover:translate-y-[4px] active:translate-y-[8px] active:shadow-none sm:py-6 sm:text-3xl lg:py-4 lg:text-2xl"
                style={{ backgroundColor: config.primary }}
              >
                MULAI!

                <span className="text-2xl transition-transform group-hover:scale-110 sm:text-4xl lg:text-3xl">
                  🧩
                </span>
              </button>
            </div>
          </div>
        </div>
      )}

      <style>{`
        @keyframes character-float {
          0%, 100% {
            transform: translateY(0);
          }

          50% {
            transform: translateY(-10px);
          }
        }

        @keyframes character-float-delayed {
          0%, 100% {
            transform: translateY(-4px);
          }

          50% {
            transform: translateY(6px);
          }
        }

        @keyframes pop {
          0% {
            transform: scale(0.85);
            opacity: 0;
          }

          70% {
            transform: scale(1.03);
            opacity: 1;
          }

          100% {
            transform: scale(1);
            opacity: 1;
          }
        }

        @keyframes fade-in {
          from {
            opacity: 0;
          }

          to {
            opacity: 1;
          }
        }

        .animate-character-float {
          animation: character-float 5s infinite ease-in-out;
        }

        .animate-character-float-delayed {
          animation: character-float-delayed 5.4s infinite ease-in-out;
        }

        .animate-pop {
          animation: pop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.15)
            forwards;
        }

        .animate-fade-in {
          animation: fade-in 0.25s ease-out forwards;
        }

        @media (prefers-reduced-motion: reduce) {
          .animate-character-float,
          .animate-character-float-delayed,
          .animate-pop,
          .animate-fade-in {
            animation: none !important;
          }
        }
      `}</style>
    </div>
  );
};

export default LevelSelector;