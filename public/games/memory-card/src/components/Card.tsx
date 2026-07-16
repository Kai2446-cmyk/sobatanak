import type { GameCard, ThemeName } from '../types/game';

interface CardProps {
  card: GameCard;
  onClick: () => void;
  disabled: boolean;
  size?: 'small' | 'medium' | 'large';
  themeName: ThemeName;
}

export function Card({ card, onClick, disabled, size = 'medium', themeName }: CardProps) {
  const hasShakeAnimation = card.isFlipped && !card.isMatched;
  
  // Tentukan gambar back card dan warna background sesuai tema
  const isSolisTheme = themeName === 'animals' || themeName === 'fruits';
  const backCardImage = isSolisTheme ? '/games/memory-card/dist/card-back/solis-card-back.png' : '/games/memory-card/dist/card-back/selena-card-back.png';
  const backgroundColor = isSolisTheme ? '#FEF3C7' : '#E6E6FA'; // Soft yellow untuk Solis, Soft purple untuk Selena
  const objectPosition = isSolisTheme ? 'center' : '55% center';

  const sizeClass = {
    small: {
      radius: 'rounded-xl md:rounded-2xl',
      border: 'border-[3px] md:border-4',
      question: 'text-2xl md:text-3xl',
      image: 'w-[72%] h-[72%]',
      label: 'text-[10px] md:text-xs',
      check: 'w-5 h-5 md:w-7 md:h-7 text-xs md:text-sm',
      padding: 'p-1.5 md:p-2',
    },
    medium: {
      radius: 'rounded-xl md:rounded-2xl',
      border: 'border-[3px] md:border-4',
      question: 'text-3xl md:text-4xl',
      image: 'w-[74%] h-[74%]',
      label: 'text-xs md:text-sm',
      check: 'w-6 h-6 md:w-8 md:h-8 text-sm md:text-base',
      padding: 'p-2',
    },
    large: {
      radius: 'rounded-2xl md:rounded-[1.35rem]',
      border: 'border-4',
      question: 'text-4xl md:text-5xl',
      image: 'w-3/4 h-3/4',
      label: 'text-sm md:text-base',
      check: 'w-7 h-7 md:w-9 md:h-9 text-sm md:text-base',
      padding: 'p-2.5 md:p-3',
    },
  };

  const currentSize = sizeClass[size];

  return (
    <div
      onClick={onClick}
      className={`
        relative cursor-pointer w-full h-full min-h-0 aspect-[3/4]
        ${disabled || card.isMatched ? 'pointer-events-none' : ''}
        ${hasShakeAnimation ? 'shake-wrong' : ''}
      `}
    >
      <div
        className={`
          w-full h-full transition-transform duration-500 transform-style-3d
          ${card.isFlipped || card.isMatched ? 'rotate-y-180' : ''}
        `}
      >
        {/* Back of card */}
        <div
          className={`
            absolute inset-0 w-full h-full backface-hidden
            ${currentSize.radius}
            ${currentSize.border}
            shadow-lg hover:shadow-xl transition-shadow duration-300
            border-white/50
            overflow-hidden
            flex items-center justify-center
          `}
          style={{ backgroundColor: backgroundColor }}
        >
          <img
            src={backCardImage}
            alt="Back Card"
            className="object-contain"
            style={{ 
              width: size === 'large' ? '80%' : size === 'medium' ? '70%' : '60%',
              height: 'auto',
              objectPosition: objectPosition 
            }}
          />
        </div>

        {/* Front of card */}
        <div
          className={`
            absolute inset-0 w-full h-full backface-hidden rotate-y-180
            ${currentSize.radius}
            ${currentSize.border}
            shadow-lg
            ${card.isMatched ? 'matched-card bg-green-400' : 'bg-white'}
            ${card.isMatched ? 'border-green-300' : 'border-amber-200'}
          `}
        >
          <div
            className={`
              w-full h-full flex flex-col items-center justify-center
              ${currentSize.padding}
            `}
          >
            <img
              src={card.image}
              alt={card.name}
              className={`
                ${currentSize.image}
                object-contain pointer-events-none user-select-none
              `}
              onError={(e) => {
                const target = e.target as HTMLImageElement;
                target.style.display = 'none';
              }}
            />

            <p
              className={`
                card-label
                ${currentSize.label}
                font-bold text-gray-700 mt-1 text-center leading-tight
                line-clamp-1
              `}
            >
              {card.name}
            </p>
          </div>

          {card.isMatched && (
            <div
              className={`
                absolute -top-1 -right-1
                ${currentSize.check}
                bg-green-400 rounded-full flex items-center justify-center
                shadow-lg border-2 border-white
              `}
            >
              <span className="text-white">✓</span>
            </div>
          )}
        </div>
      </div>

      <style>{`
        .transform-style-3d {
          transform-style: preserve-3d;
        }

        .backface-hidden {
          backface-visibility: hidden;
        }

        .rotate-y-180 {
          transform: rotateY(180deg);
        }

        @keyframes matched-pop {
          0% {
            transform: scale(0.5);
          }
          50% {
            transform: scale(1.2);
          }
          100% {
            transform: scale(1);
          }
        }

        .animate-matched-pop {
          animation: matched-pop 0.4s ease-out;
        }

        @keyframes shake-wrong {
          0%, 100% {
            transform: translateX(0) rotateY(180deg);
          }
          20% {
            transform: translateX(-5px) rotateY(180deg);
          }
          40% {
            transform: translateX(5px) rotateY(180deg);
          }
          60% {
            transform: translateX(-5px) rotateY(180deg);
          }
          80% {
            transform: translateX(5px) rotateY(180deg);
          }
        }

        .shake-wrong {
          animation: shake-wrong 0.4s ease-in-out;
        }
      `}</style>
    </div>
  );
}