import React, { useEffect, useState } from 'react';
import { DifficultyLevel } from '../types';

interface DifficultySelectorProps {
  onSelect: (difficulty: DifficultyLevel) => void;
  onBack: () => void;
}

const DifficultySelector: React.FC<DifficultySelectorProps> = ({
  onSelect,
  onBack,
}) => {
  const [mounted, setMounted] = useState(false);

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

  const difficulties = [
    {
      level: 'mudah' as DifficultyLevel,
      label: 'Mudah',
      pieces: 8,
      color: 'bg-[#ECFDF5] text-[#81C784] border-[#81C784]',
      hoverColor: 'hover:bg-[#81C784] hover:text-white',
      description: '8 keping puzzle',
      borderShadow: 'shadow-[0_8px_0_0_#81C784]',
      hoverShadow: 'hover:shadow-[0_4px_0_0_#81C784]',
      character: '/games/puzzle/dist/assets/ekspresi/selena-netral.png',
      characterAlt: 'Selena tersenyum',
      characterNote: 'Cocok untuk mulai belajar',
    },
    {
      level: 'sedang' as DifficultyLevel,
      label: 'Sedang',
      pieces: 12,
      color: 'bg-[#FFF7ED] text-[#F97316] border-[#F97316]',
      hoverColor: 'hover:bg-[#F97316] hover:text-white',
      description: '12 keping puzzle',
      borderShadow: 'shadow-[0_8px_0_0_#F97316]',
      hoverShadow: 'hover:shadow-[0_4px_0_0_#F97316]',
      character: '/games/puzzle/dist/assets/ekspresi/solis-bahagia.png',
      characterAlt: 'Solis tersenyum',
      characterNote: 'Perlu fokus dan ketelitian',
    },
    {
      level: 'sulit' as DifficultyLevel,
      label: 'Sulit',
      pieces: 16,
      color: 'bg-[#FEF2F2] text-[#EF4444] border-[#EF4444]',
      hoverColor: 'hover:bg-[#EF4444] hover:text-white',
      description: '16 keping puzzle',
      borderShadow: 'shadow-[0_8px_0_0_#EF4444]',
      hoverShadow: 'hover:shadow-[0_4px_0_0_#EF4444]',
      character: '/games/puzzle/dist/assets/ekspresi/solis-semangat.png',
      characterAlt: 'Solis memberi semangat',
      characterNote: 'Siap menerima tantangan?',
    },
  ];

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

      {/* Dekorasi */}
      <div className="pointer-events-none absolute left-[5%] top-[10%] select-none text-7xl opacity-20">
        🌿
      </div>

      <div className="pointer-events-none absolute bottom-[10%] right-[5%] select-none text-8xl opacity-20">
        🌳
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
          w-[260px] xl:block 2xl:left-8 2xl:w-[320px]
          transition-opacity duration-700
          ${mounted ? 'opacity-100' : 'opacity-0'}
        `}
      >
        <div className="relative overflow-visible">
          <div className="absolute bottom-6 left-1/2 h-10 w-[65%] -translate-x-1/2 rounded-full bg-[#F9C642]/25" />

          <img
            src="/games/puzzle/dist/assets/karakter/solis-semangat.png"
            alt="Solis memberi semangat"
            draggable={false}
            className="relative z-10 h-auto w-full object-contain drop-shadow-[0_18px_20px_rgba(44,78,144,0.18)] animate-character-float"
          />

          <div className="absolute bottom-full left-1/2 z-20 mb-3 -translate-x-1/2">
            <div className="relative w-[190px] rounded-[1.5rem] border-2 border-[#F7C84A]/50 bg-white px-5 py-3.5 shadow-[0_12px_30px_rgba(61,76,145,0.20)]">
              <p className="text-center font-fredoka text-sm leading-snug text-[#3C5A9A]">
                Pilih tantangan yang kamu suka!
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
          w-[260px] xl:block 2xl:right-8 2xl:w-[320px]
          transition-opacity duration-700 delay-150
          ${mounted ? 'opacity-100' : 'opacity-0'}
        `}
      >
        <div className="relative overflow-visible">
          <div className="absolute bottom-6 left-1/2 h-10 w-[65%] -translate-x-1/2 rounded-full bg-[#8E7AE6]/25" />

          <img
            src="/games/puzzle/dist/assets/karakter/selena-menunjuk.png"
            alt="Selena memberi petunjuk"
            draggable={false}
            className="relative z-10 h-auto w-full object-contain drop-shadow-[0_18px_20px_rgba(83,73,151,0.18)] animate-character-float-delayed"
          />

          <div className="absolute bottom-full left-1/2 z-20 mb-3 -translate-x-1/2">
            <div className="relative w-[200px] rounded-[1.5rem] border-2 border-[#A594E9]/50 bg-white px-5 py-3.5 shadow-[0_12px_30px_rgba(61,76,145,0.20)]">
              <p className="text-center font-fredoka text-sm leading-snug text-[#6757B6]">
                Mulai dari level mudah juga tidak apa-apa.
              </p>

              <span className="absolute -bottom-2 left-1/2 h-4 w-4 -translate-x-1/2 rotate-45 border-b-2 border-r-2 border-[#A594E9]/50 bg-white" />
            </div>
          </div>
        </div>
      </div>

      {/* Konten utama */}
      <div className="relative z-20 mx-auto flex w-full max-w-4xl flex-1 flex-col lg:min-h-0 lg:justify-center lg:px-2 lg:pb-14">
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
            aria-label="Kembali"
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
              Pilih Level
            </h1>

            <p className="mt-2 text-xs font-bold uppercase tracking-[0.25em] text-[#1E2939]/40 sm:text-sm lg:tracking-[0.2em]">
              Tentukan tingkat kesulitan!
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
                  Pilih tantanganmu!
                </p>
              </div>

              <img
                src="/games/puzzle/dist/assets/karakter/solis-semangat.png"
                alt="Solis memberi semangat"
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
                  Mulai dari mudah juga boleh.
                </p>
              </div>

              <img
                src="/games/puzzle/dist/assets/karakter/selena-menunjuk.png"
                alt="Selena memberi petunjuk"
                draggable={false}
                className="relative z-10 h-[145px] w-auto object-contain sm:h-[180px]"
              />
            </div>
          </div>
        </div>

        {/* Kartu level */}
        <div className="relative grid grid-cols-1 gap-8 md:grid-cols-3 lg:gap-5">
          {difficulties.map((diff, index) => (
            <button
              type="button"
              key={diff.level}
              onClick={() => onSelect(diff.level)}
              className={`
                group relative
                cursor-pointer rounded-[3rem]
                border-4 bg-white p-8
                text-left shadow-lg
                transition-[transform,opacity,box-shadow] duration-500
                hover:-translate-y-3 hover:shadow-xl
                focus:outline-none focus-visible:ring-4
                focus-visible:ring-[#81C784]/30
                focus-visible:ring-offset-4
                lg:rounded-[2.5rem] lg:p-6
                ${diff.color}
                ${mounted ? 'opacity-100' : 'opacity-0'}
              `}
              style={{
                transitionDelay: `${index * 100}ms`,
              }}
            >
              <div className="absolute inset-0 -z-10 rounded-full bg-current opacity-0 transition-opacity duration-700 group-hover:opacity-[0.08]" />

              <div className="text-center">
                <div className="mx-auto mb-4 flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-4 border-current/20 bg-white shadow-md transition-transform duration-300 group-hover:scale-105 lg:mb-3 lg:h-20 lg:w-20">
                  <img
                    src={diff.character}
                    alt={diff.characterAlt}
                    draggable={false}
                    className="h-full w-full object-cover"
                  />
                </div>

                <h3 className="mb-2 font-fredoka text-4xl lg:text-3xl">
                  {diff.label}
                </h3>

                <div
                  className={`
                    mb-3 inline-block rounded-full
                    px-6 py-2 text-lg font-bold
                    lg:px-5 lg:py-1.5 lg:text-base
                    ${diff.color}
                  `}
                >
                  {diff.description}
                </div>

                <p className="mb-6 min-h-[42px] text-sm font-semibold text-[#1E2939]/55 lg:mb-4 lg:min-h-0 lg:text-xs">
                  {diff.characterNote}
                </p>

                <div
                  className={`
                    w-full rounded-[2rem]
                    bg-white py-4 text-center
                    font-fredoka text-2xl
                    transition-[transform,box-shadow,background-color,color]
                    group-hover:translate-y-1
                    group-active:translate-y-2
                    group-active:shadow-none
                    lg:py-3 lg:text-xl
                    ${diff.color.split(' ')[1]}
                    ${diff.hoverColor}
                    ${diff.borderShadow}
                    ${diff.hoverShadow}
                  `}
                >
                  Pilih
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
            <span className="hidden h-px w-6 bg-[#81C784]/60 sm:block lg:w-10" />
            Pilih salah satu level untuk melanjutkan
            <span className="hidden h-px w-6 bg-[#81C784]/60 sm:block lg:w-10" />
          </p>
        </div>
      </div>

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

        .animate-character-float {
          animation: character-float 5s infinite ease-in-out;
        }

        .animate-character-float-delayed {
          animation: character-float-delayed 5.4s infinite ease-in-out;
        }

        @media (prefers-reduced-motion: reduce) {
          .animate-character-float,
          .animate-character-float-delayed {
            animation: none !important;
          }
        }
      `}</style>
    </div>
  );
};

export default DifficultySelector;