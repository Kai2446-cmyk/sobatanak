import React, { useState, useEffect, useRef, useCallback, useMemo } from 'react';
import { PuzzleData, PieceState } from '../types';
import { generateJigsawPath } from '../utils/jigsawPath';
import { SNAP_THRESHOLD, GAME_TIME_SECONDS } from '../constants';
import SuccessModal from './SuccessModal';

interface GameBoardProps {
  puzzle: PuzzleData;
  onExit: () => void;
  onWin: () => void;
}

interface WrongFeedback {
  x: number;
  y: number;
  id: number;
}

// CHARACTER FEEDBACK START
type CharacterId = 'solis' | 'selena';

type FeedbackKey =
  | 'correct'
  | 'combo'
  | 'progress_75'
  | 'time_low'
  | 'wrong'
  | 'hint'
  | 'pause'
  | 'idle';

interface ActiveCharacterFeedback {
  character: CharacterId;
  message: string;
  image: string;
}

const FEEDBACK_DURATION_MS = 2500;
const IDLE_THRESHOLD_MS = 12000;
const TIME_LOW_SECONDS = 15;
const COMBO_WINDOW_MS = 5000;
const COMBO_REQUIRED = 3;

const FEEDBACK_CONFIG: Record<
  FeedbackKey,
  { character: CharacterId; message: string; image: string }
> = {
  correct: {
    character: 'solis',
    message: 'Bagus sekali! Potongan yang tepat!',
    image: '/games/puzzle/dist/assets/ekspresi/solis-bahagia.png',
  },
  combo: {
    character: 'solis',
    message: 'Wah combo! Kamu luar biasa!',
    image: '/games/puzzle/dist/assets/ekspresi/solis-semangat.png',
  },
  progress_75: {
    character: 'solis',
    message: 'Hampir selesai! Terus semangat ya!',
    image: '/games/puzzle/dist/assets/ekspresi/solis-semangat.png',
  },
  time_low: {
    character: 'solis',
    message: 'Waktu hampir habis! Fokus ya!',
    image: '/games/puzzle/dist/assets/ekspresi/solis-semangat.png',
  },
  wrong: {
    character: 'selena',
    message: 'Belum tepat, coba lagi ya.',
    image: '/games/puzzle/dist/assets/ekspresi/selena-netral.png',
  },
  hint: {
    character: 'selena',
    message: 'Seret kepingan ke tempat yang pas.',
    image: '/games/puzzle/dist/assets/karakter/selena-menunjuk.png',
  },
  pause: {
    character: 'selena',
    message: 'Bersiaplah, game akan segera dimulai!',
    image: '/games/puzzle/dist/assets/ekspresi/selena-berpikir.png',
  },
  idle: {
    character: 'selena',
    message: 'Masih di sini? Yuk lanjut susun!',
    image: '/games/puzzle/dist/assets/ekspresi/selena-netral.png',
  },
};

const SOLIS_DEFAULT_IMAGE = '/games/puzzle/dist/assets/karakter/solis-melambai.png';
const SELENA_DEFAULT_IMAGE = '/games/puzzle/dist/assets/karakter/selena-menunjuk.png';
// CHARACTER FEEDBACK END

const MAX_PIECE_SIZE = 110;
const MIN_PIECE_SIZE = 64;
const BOARD_BORDER = 10;
const PIECE_PADDING = 42;
const SHELF_HEIGHT = 190;
const SIDEBAR_WIDTH = 232;

// Ukuran visual minimal papan.
// Level mudah tetap jumlah piece sedikit, tetapi ukuran papan disamakan dengan level sedang.
const MIN_VISUAL_ROWS = 3;
const MIN_VISUAL_COLS = 4;

const GameBoard: React.FC<GameBoardProps> = ({ puzzle, onExit, onWin }) => {
  const [pieces, setPieces] = useState<PieceState[]>([]);
  const [isFinished, setIsFinished] = useState(false);
  const [isTimeUp, setIsTimeUp] = useState(false);
  const [activePieceId, setActivePieceId] = useState<string | null>(null);
  const [wrongFeedback, setWrongFeedback] = useState<WrongFeedback | null>(null);

  // CHARACTER FEEDBACK START
  const [characterFeedback, setCharacterFeedback] =
    useState<ActiveCharacterFeedback | null>(null);
  const [charMounted, setCharMounted] = useState(false);

  const feedbackTimerRef = useRef<number | null>(null);
  const progress75ShownRef = useRef(false);
  const timeLowShownRef = useRef(false);
  const idleShownRef = useRef(false);
  const hintShownRef = useRef(false);
  const pauseShownRef = useRef(false);
  const consecutiveCorrectRef = useRef(0);
  const lastCorrectAtRef = useRef(0);
  const lastActivityRef = useRef(Date.now());
  const idleCheckTimerRef = useRef<number | null>(null);
  // CHARACTER FEEDBACK END

  const [isStarting, setIsStarting] = useState(true);
  const [countdown, setCountdown] = useState<number | 'GO' | null>(null);

  const [score, setScore] = useState(0);
  const [timeLeft, setTimeLeft] = useState(GAME_TIME_SECONDS);
  const [scoreAnim, setScoreAnim] = useState(false);

  const [baseUnitSize, setBaseUnitSize] = useState(MAX_PIECE_SIZE);
  const [boardOffset, setBoardOffset] = useState({ x: 0, y: 0 });

  const tableRef = useRef<HTMLDivElement>(null);
  const scoreAnimTimerRef = useRef<number | null>(null);
  const wrongFeedbackTimerRef = useRef<number | null>(null);

  const totalPieces = pieces.length;
  const lockedPieces = useMemo(() => pieces.filter((p) => p.isLocked).length, [pieces]);

  const visualRows = Math.max(puzzle.rows, MIN_VISUAL_ROWS);
  const visualCols = Math.max(puzzle.cols, MIN_VISUAL_COLS);

  // Ukuran papan visual final.
  const boardWidth = visualCols * baseUnitSize;
  const boardHeight = visualRows * baseUnitSize;

  // Ukuran actual tiap keping agar level mudah tetap mengisi board penuh.
  // Contoh easy 2x3 pada board 3x4: piece otomatis lebih tinggi/lebar sehingga tidak ada space kosong.
  const pieceWidth = boardWidth / puzzle.cols;
  const pieceHeight = boardHeight / puzzle.rows;
  const maxPieceDimension = Math.max(pieceWidth, pieceHeight);

  const isGameActive = !isFinished && !isTimeUp && countdown === null && !isStarting;

  // CHARACTER FEEDBACK START
  const resetCharacterFeedbackFlags = useCallback(() => {
    progress75ShownRef.current = false;
    timeLowShownRef.current = false;
    idleShownRef.current = false;
    hintShownRef.current = false;
    pauseShownRef.current = false;
    consecutiveCorrectRef.current = 0;
    lastCorrectAtRef.current = 0;
    lastActivityRef.current = Date.now();
  }, []);

  const markActivity = useCallback(() => {
    lastActivityRef.current = Date.now();
  }, []);

  const showFeedback = useCallback((key: FeedbackKey) => {
    if (key === 'progress_75' && progress75ShownRef.current) return;
    if (key === 'time_low' && timeLowShownRef.current) return;
    if (key === 'idle' && idleShownRef.current) return;
    if (key === 'hint' && hintShownRef.current) return;
    if (key === 'pause' && pauseShownRef.current) return;

    const config = FEEDBACK_CONFIG[key];

    if (key === 'progress_75') progress75ShownRef.current = true;
    if (key === 'time_low') timeLowShownRef.current = true;
    if (key === 'idle') idleShownRef.current = true;
    if (key === 'hint') hintShownRef.current = true;
    if (key === 'pause') pauseShownRef.current = true;

    if (feedbackTimerRef.current) {
      window.clearTimeout(feedbackTimerRef.current);
    }

    setCharacterFeedback({
      character: config.character,
      message: config.message,
      image: config.image,
    });

    feedbackTimerRef.current = window.setTimeout(() => {
      setCharacterFeedback(null);
      feedbackTimerRef.current = null;
    }, FEEDBACK_DURATION_MS);
  }, []);

  const handleCorrectPieceFeedback = useCallback(
    (nextLockedCount: number) => {
      const now = Date.now();
      const progressRatio = totalPieces > 0 ? nextLockedCount / totalPieces : 0;

      if (progressRatio >= 0.75 && !progress75ShownRef.current) {
        showFeedback('progress_75');
        return;
      }

      if (now - lastCorrectAtRef.current <= COMBO_WINDOW_MS) {
        consecutiveCorrectRef.current += 1;
      } else {
        consecutiveCorrectRef.current = 1;
      }

      lastCorrectAtRef.current = now;

      if (consecutiveCorrectRef.current >= COMBO_REQUIRED) {
        consecutiveCorrectRef.current = 0;
        showFeedback('combo');
        return;
      }

      showFeedback('correct');
    },
    [showFeedback, totalPieces]
  );
  // CHARACTER FEEDBACK END

  const calculateBoardLayout = useCallback(() => {
    if (!tableRef.current) return;

    const rect = tableRef.current.getBoundingClientRect();

    const safePaddingX = 80;
    const safePaddingY = 80;

    const availableW = Math.max(200, rect.width - safePaddingX);
    const availableH = Math.max(200, rect.height - safePaddingY);

    const nextVisualCols = Math.max(puzzle.cols, MIN_VISUAL_COLS);
    const nextVisualRows = Math.max(puzzle.rows, MIN_VISUAL_ROWS);

    const nextBaseUnitSize = Math.floor(
      Math.min(
        MAX_PIECE_SIZE,
        Math.max(
          MIN_PIECE_SIZE,
          Math.min(availableW / nextVisualCols, availableH / nextVisualRows)
        )
      )
    );

    const nextBoardW = nextVisualCols * nextBaseUnitSize;
    const nextBoardH = nextVisualRows * nextBaseUnitSize;

    setBaseUnitSize(nextBaseUnitSize);
    setBoardOffset({
      x: (rect.width - nextBoardW) / 2,
      y: (rect.height - nextBoardH) / 2,
    });
  }, [puzzle.cols, puzzle.rows]);

  const createInitialPieces = useCallback(() => {
    const windowWidth = window.innerWidth;
    const windowHeight = window.innerHeight;
    const total = puzzle.rows * puzzle.cols;

    // Kepingan tetap diacak seperti permainan puzzle, tetapi titik acaknya
    // dibatasi di area rak agar tidak keluar layar atau menumpuk berlebihan.
    const trayLeft = 30;
    const trayRight = Math.max(trayLeft + pieceWidth, windowWidth - SIDEBAR_WIDTH - 26);
    const trayWidth = trayRight - trayLeft;
    const trayTop = windowHeight - SHELF_HEIGHT + 34;
    const trayUsableHeight = Math.max(pieceHeight, SHELF_HEIGHT - 58);

    const minStepX = Math.max(pieceWidth * 0.72, 76);
    const columns = Math.max(1, Math.min(total, Math.floor(trayWidth / minStepX)));
    const rows = Math.max(1, Math.ceil(total / columns));
    const stepX = columns > 1 ? (trayWidth - pieceWidth) / (columns - 1) : 0;
    const stepY = rows > 1 ? Math.max(18, (trayUsableHeight - pieceHeight) / (rows - 1)) : 0;

    // Shuffle deterministik: susunan tampak acak, tetapi tidak berubah saat React render ulang.
    const shuffledSlots = Array.from({ length: total }, (_, index) => index);
    let seed = puzzle.rows * 97 + puzzle.cols * 53 + puzzle.title.length * 17;
    const nextRandom = () => {
      seed = (seed * 9301 + 49297) % 233280;
      return seed / 233280;
    };

    for (let i = shuffledSlots.length - 1; i > 0; i--) {
      const j = Math.floor(nextRandom() * (i + 1));
      [shuffledSlots[i], shuffledSlots[j]] = [shuffledSlots[j], shuffledSlots[i]];
    }

    const newPieces: PieceState[] = [];

    for (let r = 0; r < puzzle.rows; r++) {
      for (let c = 0; c < puzzle.cols; c++) {
        const id = `piece-${r}-${c}`;
        const index = r * puzzle.cols + c;
        const slot = shuffledSlots[index];
        const trayCol = slot % columns;
        const trayRow = Math.floor(slot / columns);
        const jitterX = (nextRandom() - 0.5) * Math.min(34, Math.max(10, stepX * 0.22));
        const jitterY = (nextRandom() - 0.5) * Math.min(22, Math.max(8, stepY * 0.35));
        const x = Math.min(
          trayRight - pieceWidth,
          Math.max(trayLeft, trayLeft + trayCol * stepX + jitterX)
        );
        const y = Math.min(
          windowHeight - pieceHeight - 12,
          Math.max(trayTop, trayTop + trayRow * stepY + jitterY)
        );

        newPieces.push({
          id,
          row: r,
          col: c,
          currentPos: { x, y },
          targetPos: {
            x: c * pieceWidth,
            y: r * pieceHeight,
          },
          isLocked: false,
          zIndex: 10 + Math.floor(nextRandom() * total),
        });
      }
    }

    setPieces(newPieces);
  }, [puzzle, pieceWidth, pieceHeight]);

  // Reset game hanya saat puzzle/level berubah.
  // Resize layar tidak boleh reset score, time, progress, atau posisi locked.
  useEffect(() => {
    setIsFinished(false);
    setIsTimeUp(false);
    setActivePieceId(null);
    setWrongFeedback(null);
    setIsStarting(true);
    setCountdown(null);
    setScore(0);
    setTimeLeft(GAME_TIME_SECONDS);
    setScoreAnim(false);
    // CHARACTER FEEDBACK START
    setCharacterFeedback(null);
    resetCharacterFeedbackFlags();
    if (feedbackTimerRef.current) {
      window.clearTimeout(feedbackTimerRef.current);
      feedbackTimerRef.current = null;
    }
    // CHARACTER FEEDBACK END
    createInitialPieces();
  }, [puzzle]);

  // Hitung ulang ukuran board saat mount, saat puzzle berubah, dan saat browser resize.
  // Ini hanya mengubah ukuran/layout, bukan regenerate pieces.
  useEffect(() => {
    const timeout = window.setTimeout(() => {
      calculateBoardLayout();
    }, 50);

    window.addEventListener('resize', calculateBoardLayout);

    return () => {
      window.clearTimeout(timeout);
      window.removeEventListener('resize', calculateBoardLayout);
    };
  }, [calculateBoardLayout]);

  // Saat ukuran board berubah karena resize:
  // - targetPos setiap piece diupdate.
  // - piece yang sudah locked tetap locked dan ikut pindah ke slot baru.
  // - piece yang belum locked tidak dibuat ulang, jadi progress/score/timer aman.
  useEffect(() => {
    setPieces((prev) => {
      if (prev.length === 0) return prev;

      const tableRect = tableRef.current?.getBoundingClientRect();

      return prev.map((p) => {
        const nextTargetPos = {
          x: p.col * pieceWidth,
          y: p.row * pieceHeight,
        };

        if (p.isLocked && tableRect) {
          return {
            ...p,
            targetPos: nextTargetPos,
            currentPos: {
              x: tableRect.left + boardOffset.x + nextTargetPos.x,
              y: tableRect.top + boardOffset.y + nextTargetPos.y,
            },
          };
        }

        return {
          ...p,
          targetPos: nextTargetPos,
        };
      });
    });
  }, [pieceWidth, pieceHeight, boardOffset.x, boardOffset.y]);

  useEffect(() => {
    if (!isGameActive) return;

    const interval = window.setInterval(() => {
      setTimeLeft((prev) => {
        if (prev <= 1) {
          window.clearInterval(interval);
          setIsTimeUp(true);
          return 0;
        }

        return prev - 1;
      });
    }, 1000);

    return () => window.clearInterval(interval);
  }, [isGameActive]);

  useEffect(() => {
    return () => {
      if (scoreAnimTimerRef.current) window.clearTimeout(scoreAnimTimerRef.current);
      if (wrongFeedbackTimerRef.current) window.clearTimeout(wrongFeedbackTimerRef.current);
      // CHARACTER FEEDBACK START
      if (feedbackTimerRef.current) window.clearTimeout(feedbackTimerRef.current);
      if (idleCheckTimerRef.current) window.clearInterval(idleCheckTimerRef.current);
      // CHARACTER FEEDBACK END
    };
  }, []);

  // CHARACTER FEEDBACK START
  useEffect(() => {
    const timer = window.setTimeout(() => setCharMounted(true), 80);
    return () => window.clearTimeout(timer);
  }, []);

  useEffect(() => {
    if (isStarting) {
      showFeedback('pause');
    }
  }, [isStarting, showFeedback]);

  useEffect(() => {
    if (!isGameActive) return;

    const timer = window.setTimeout(() => {
      showFeedback('hint');
    }, 900);

    return () => window.clearTimeout(timer);
  }, [isGameActive, showFeedback]);

  useEffect(() => {
    if (!isGameActive) return;
    if (timeLeft <= TIME_LOW_SECONDS && timeLeft > 0) {
      showFeedback('time_low');
    }
  }, [timeLeft, isGameActive, showFeedback]);

  useEffect(() => {
    if (!isGameActive) {
      if (idleCheckTimerRef.current) {
        window.clearInterval(idleCheckTimerRef.current);
        idleCheckTimerRef.current = null;
      }
      return;
    }

    idleCheckTimerRef.current = window.setInterval(() => {
      if (Date.now() - lastActivityRef.current >= IDLE_THRESHOLD_MS) {
        showFeedback('idle');
      }
    }, 1000);

    return () => {
      if (idleCheckTimerRef.current) {
        window.clearInterval(idleCheckTimerRef.current);
        idleCheckTimerRef.current = null;
      }
    };
  }, [isGameActive, showFeedback]);
  // CHARACTER FEEDBACK END

  const startCountdown = () => {
    setIsStarting(false);

    let count = 3;
    setCountdown(count);

    const timer = window.setInterval(() => {
      count -= 1;

      if (count > 0) {
        setCountdown(count);
      } else if (count === 0) {
        setCountdown('GO');
      } else {
        window.clearInterval(timer);
        setCountdown(null);
      }
    }, 1000);
  };

  const resetGame = () => {
    setIsFinished(false);
    setIsTimeUp(false);
    setActivePieceId(null);
    setWrongFeedback(null);
    setIsStarting(true);
    setCountdown(null);
    setScore(0);
    setTimeLeft(GAME_TIME_SECONDS);
    setScoreAnim(false);
    // CHARACTER FEEDBACK START
    setCharacterFeedback(null);
    resetCharacterFeedbackFlags();
    if (feedbackTimerRef.current) {
      window.clearTimeout(feedbackTimerRef.current);
      feedbackTimerRef.current = null;
    }
    // CHARACTER FEEDBACK END
    createInitialPieces();
  };

  const triggerScoreAnimation = () => {
    setScoreAnim(true);

    if (scoreAnimTimerRef.current) {
      window.clearTimeout(scoreAnimTimerRef.current);
    }

    scoreAnimTimerRef.current = window.setTimeout(() => {
      setScoreAnim(false);
    }, 500);
  };

  const showWrongFeedback = (x: number, y: number) => {
    // CHARACTER FEEDBACK START
    showFeedback('wrong');
    // CHARACTER FEEDBACK END

    setWrongFeedback({
      x,
      y,
      id: Date.now(),
    });

    if (wrongFeedbackTimerRef.current) {
      window.clearTimeout(wrongFeedbackTimerRef.current);
    }

    wrongFeedbackTimerRef.current = window.setTimeout(() => {
      setWrongFeedback(null);
    }, 800);
  };

  const handleMouseDown = (
    id: string,
    e: React.MouseEvent<HTMLDivElement> | React.TouchEvent<HTMLDivElement>
  ) => {
    if (!isGameActive) return;

    const piece = pieces.find((p) => p.id === id);
    if (!piece || piece.isLocked) return;

    e.preventDefault();
    // CHARACTER FEEDBACK START
    markActivity();
    // CHARACTER FEEDBACK END

    setActivePieceId(id);

    setPieces((prev) =>
      prev.map((p) =>
        p.id === id
          ? {
              ...p,
              zIndex: 3000,
            }
          : p
      )
    );
  };

  const handleMouseMove = useCallback(
    (e: MouseEvent | TouchEvent) => {
      if (!activePieceId || !isGameActive) return;

      if ('touches' in e && e.cancelable) {
        e.preventDefault();
      }

      const clientX = 'touches' in e ? e.touches[0]?.clientX : e.clientX;
      const clientY = 'touches' in e ? e.touches[0]?.clientY : e.clientY;

      if (clientX === undefined || clientY === undefined) return;

      // CHARACTER FEEDBACK START
      markActivity();
      // CHARACTER FEEDBACK END

      const x = clientX - pieceWidth / 2;
      const y = clientY - pieceHeight / 2;

      setPieces((prev) =>
        prev.map((p) =>
          p.id === activePieceId
            ? {
                ...p,
                currentPos: { x, y },
              }
            : p
        )
      );
    },
    [activePieceId, isGameActive, pieceWidth, pieceHeight, markActivity]
  );

  const handleMouseUp = useCallback(
    (e: MouseEvent | TouchEvent) => {
      if (!activePieceId || !isGameActive) return;

      // CHARACTER FEEDBACK START
      markActivity();
      // CHARACTER FEEDBACK END

      setPieces((prev) => {
        const active = prev.find((p) => p.id === activePieceId);

        if (!active || !tableRef.current) return prev;

        const tableRect = tableRef.current.getBoundingClientRect();

        const slotScreenX = tableRect.left + boardOffset.x + active.targetPos.x;
        const slotScreenY = tableRect.top + boardOffset.y + active.targetPos.y;

        const dist = Math.sqrt(
          Math.pow(active.currentPos.x - slotScreenX, 2) +
            Math.pow(active.currentPos.y - slotScreenY, 2)
        );

        if (dist < SNAP_THRESHOLD) {
          triggerScoreAnimation();

          const updated = prev.map((p) =>
            p.id === activePieceId
              ? {
                  ...p,
                  currentPos: {
                    x: slotScreenX,
                    y: slotScreenY,
                  },
                  isLocked: true,
                  zIndex: 1,
                }
              : p
          );

          const isAllLocked = updated.every((p) => p.isLocked);
          const timeBonus = isAllLocked ? timeLeft * 50 : 0;
          const nextLockedCount = updated.filter((p) => p.isLocked).length;

          setScore((s) => s + 100 + timeBonus);

          // CHARACTER FEEDBACK START
          handleCorrectPieceFeedback(nextLockedCount);
          // CHARACTER FEEDBACK END

          if (isAllLocked) {
            window.setTimeout(() => {
              setIsFinished(true);
            }, 800);
          }

          return updated;
        }

        const clientX = 'changedTouches' in e ? e.changedTouches[0]?.clientX : e.clientX;
        const clientY = 'changedTouches' in e ? e.changedTouches[0]?.clientY : e.clientY;

        if (clientX !== undefined && clientY !== undefined) {
          const boardScreenX = tableRect.left + boardOffset.x;
          const boardScreenY = tableRect.top + boardOffset.y;

          const isNearBoard =
            clientX > boardScreenX - 40 &&
            clientX < boardScreenX + boardWidth + 40 &&
            clientY > boardScreenY - 40 &&
            clientY < boardScreenY + boardHeight + 40;

          if (isNearBoard) {
            showWrongFeedback(clientX, clientY);
          }
        }

        return prev;
      });

      setActivePieceId(null);
    },
    [
      activePieceId,
      isGameActive,
      boardOffset.x,
      boardOffset.y,
      boardWidth,
      boardHeight,
      timeLeft,
      markActivity,
      handleCorrectPieceFeedback,
    ]
  );

  useEffect(() => {
    window.addEventListener('mousemove', handleMouseMove);
    window.addEventListener('mouseup', handleMouseUp);
    window.addEventListener('touchmove', handleMouseMove, { passive: false });
    window.addEventListener('touchend', handleMouseUp);

    return () => {
      window.removeEventListener('mousemove', handleMouseMove);
      window.removeEventListener('mouseup', handleMouseUp);
      window.removeEventListener('touchmove', handleMouseMove);
      window.removeEventListener('touchend', handleMouseUp);
    };
  }, [handleMouseMove, handleMouseUp]);

  const renderBoardSlots = () => {
    const slots = [];

    for (let r = 0; r < puzzle.rows; r++) {
      for (let c = 0; c < puzzle.cols; c++) {
        const path = generateJigsawPath(
          r,
          c,
          puzzle.rows,
          puzzle.cols,
          pieceWidth,
          pieceHeight
        );

        slots.push(
          <path
            key={`slot-${r}-${c}`}
            d={path}
            transform={`translate(${c * pieceWidth}, ${r * pieceHeight})`}
            fill="#065f46"
            fillOpacity="0.06"
            stroke="#065f46"
            strokeWidth="1.6"
            strokeOpacity="0.28"
            strokeDasharray="5 5"
          />
        );
      }
    }

    return slots;
  };

  const formatTime = (seconds: number) => {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;

    return `${m}:${s.toString().padStart(2, '0')}`;
  };

  // CHARACTER FEEDBACK START
  const solisImage =
    characterFeedback?.character === 'solis'
      ? characterFeedback.image
      : SOLIS_DEFAULT_IMAGE;

  const selenaImage =
    characterFeedback?.character === 'selena'
      ? characterFeedback.image
      : SELENA_DEFAULT_IMAGE;
  // CHARACTER FEEDBACK END

  return (
    <div
      className="relative w-full h-screen flex flex-col bg-[#FBFCF8] overflow-hidden fixed inset-0 touch-none"
      style={{ touchAction: 'none' }}
    >
      <div className="h-[82px] shrink-0 px-5 md:px-8 flex justify-between items-center bg-white border-b-2 border-[#E5F4EF] z-[100] shadow-[0_6px_18px_rgba(42,61,60,0.06)]">
        <button
          onClick={onExit}
          className="bg-white border-b-4 border-[#FEF3C7] p-3 rounded-2xl text-[#DC2626] hover:bg-[#FEF3C7] active:translate-y-1 active:border-b-0 transition-all shadow-sm"
        >
          <svg className="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={3}
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
        </button>

        <div className="text-center px-3">
          <h2 className="text-2xl md:text-4xl font-fredoka text-[#1E2939] tracking-tighter uppercase line-clamp-1">
            {puzzle.title}
          </h2>
          <p className="text-[9px] md:text-[10px] font-bold text-[#81C784]/60 uppercase tracking-widest mt-0.5">
            {puzzle.rows} x {puzzle.cols} Puzzle • SobatAnak
          </p>
        </div>

        <div className="bg-[#ECFDF5] border-4 border-white rounded-2xl px-4 py-2 shadow-sm text-center">
          <p className="text-[9px] font-black text-[#81C784]/60 uppercase tracking-[0.25em]">TIME</p>
          <p className="text-2xl md:text-3xl font-fredoka text-[#81C784]">{formatTime(timeLeft)}</p>
        </div>
      </div>

      <div className="flex-1 relative flex overflow-hidden touch-none">
        <div
          ref={tableRef}
          className="flex-1 relative bg-[#FBFCF8] p-5 md:p-7 flex items-center justify-center overflow-hidden touch-none"
        >
          <div
            className="absolute inset-0 pointer-events-none"
            style={{
              backgroundImage:
                'radial-gradient(circle at 1px 1px, rgba(129,199,132,0.12) 1px, transparent 0)',
              backgroundSize: '24px 24px',
            }}
          />

          <div
            className="absolute bg-[#EFFAF6] border-[10px] border-[#5B463E] shadow-[0_24px_55px_-24px_rgba(52,42,37,0.38)] rounded-[2rem] overflow-hidden"
            style={{
              width: boardWidth + BOARD_BORDER * 2,
              height: boardHeight + BOARD_BORDER * 2,
              left: boardOffset.x - BOARD_BORDER,
              top: boardOffset.y - BOARD_BORDER,
            }}
          >
            <div className="absolute inset-0 opacity-[0.025] grayscale pointer-events-none select-none">
              <img src={puzzle.image} className="w-full h-full object-cover" alt="" draggable={false} />
            </div>

            <svg width={boardWidth} height={boardHeight} className="absolute top-0 left-0 pointer-events-none">
              {renderBoardSlots()}
            </svg>
          </div>
        </div>

        <div className="w-52 md:w-[232px] bg-white border-l-2 border-[#E5F4EF] flex flex-col items-center py-6 px-4 z-[90]">
          <div className="w-full bg-[#81C784] p-1 rounded-[1.6rem] mb-5 shadow-[0_10px_24px_rgba(129,199,132,0.2)]">
            <div className="bg-white rounded-[1.35rem] px-4 py-5 text-center border-2 border-[#ECFDF5]">
              <p className="text-[10px] font-black text-[#81C784]/60 uppercase tracking-[0.3em] mb-1">SCORE</p>
              <p
                className={`text-5xl font-fredoka text-[#81C784] transition-all duration-300 ${
                  scoreAnim ? 'scale-125 text-[#FFD54F]' : 'scale-100'
                }`}
              >
                {score}
              </p>
            </div>
          </div>

          <div className="flex-1 space-y-4 w-full">
            <div className="bg-white p-4 rounded-[1.5rem] border-2 border-[#ECFDF5] shadow-sm">
              <p className="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Progress</p>

              <div className="h-4 w-full bg-[#ECFDF5] rounded-full overflow-hidden">
                <div
                  className="h-full bg-[#81C784] transition-all duration-500"
                  style={{
                    width: totalPieces > 0 ? `${(lockedPieces / totalPieces) * 100}%` : '0%',
                  }}
                />
              </div>

              <p className="text-right text-xs font-bold text-[#81C784] mt-2">
                {lockedPieces}/{totalPieces} Pieces
              </p>
            </div>

            <div className="bg-[#FFF5CF] p-4 rounded-[1.5rem] border-2 border-[#FCECB0] shadow-sm animate-pulse-slow">
              <p className="text-[9px] font-bold text-[#FFA500] uppercase tracking-widest mb-1">Time Bonus</p>
              <p className="text-2xl font-fredoka text-[#FFA500]">+{timeLeft * 50}</p>
            </div>

            <div className="pt-4 flex flex-col items-center opacity-15 pointer-events-none">
              <div className="text-6xl mb-2">🌿</div>
              <p className="font-fredoka text-[#1E2939] text-sm">SobatAnak</p>
            </div>
          </div>
        </div>
      </div>

      {/* CHARACTER FEEDBACK START */}
      <div
        className={`
          pointer-events-none fixed bottom-[8.75rem] left-4 z-[85]
          w-[96px] transition-opacity duration-700
          sm:left-6 sm:w-[112px]
          md:bottom-[9rem] md:left-10 md:w-[136px]
          lg:left-14 lg:w-[156px]
          ${charMounted ? 'opacity-100' : 'opacity-0'}
        `}
      >
        <div className="relative overflow-visible">
          {characterFeedback?.character === 'solis' && (
            <div
              aria-live="polite"
              className="absolute bottom-full left-1/2 z-20 mb-2 w-[max(9rem,calc(100vw-12rem))] max-w-[11rem] -translate-x-1/2 rounded-xl border-2 border-[#F7C84A]/50 bg-white px-3 py-2 shadow-[0_10px_24px_rgba(61,76,145,0.18)] sm:max-w-[12rem]"
            >
              <p className="text-center font-fredoka text-[10px] leading-snug text-[#3C5A9A] sm:text-xs">
                {characterFeedback.message}
              </p>
              <span className="absolute -bottom-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rotate-45 border-b-2 border-r-2 border-[#F7C84A]/50 bg-white" />
            </div>
          )}

          <img
            src={solisImage}
            alt="Solis mendampingi permainan"
            draggable={false}
            className="relative z-10 h-auto w-full object-contain drop-shadow-[0_12px_16px_rgba(44,78,144,0.2)] animate-game-char-float"
          />
        </div>
      </div>

      <div
        className={`
          pointer-events-none fixed bottom-[8.75rem] right-[14.5rem] z-[85]
          w-[96px] transition-opacity duration-700 delay-100
          sm:right-[15rem] sm:w-[112px]
          md:bottom-[9rem] md:right-[15.5rem] md:w-[136px]
          lg:right-[17rem] lg:w-[156px]
          ${charMounted ? 'opacity-100' : 'opacity-0'}
        `}
      >
        <div className="relative overflow-visible">
          {characterFeedback?.character === 'selena' && (
            <div
              aria-live="polite"
              className="absolute bottom-full left-1/2 z-20 mb-2 w-[max(9rem,calc(100vw-12rem))] max-w-[11rem] -translate-x-1/2 rounded-xl border-2 border-[#A594E9]/50 bg-white px-3 py-2 shadow-[0_10px_24px_rgba(61,76,145,0.18)] sm:max-w-[12rem]"
            >
              <p className="text-center font-fredoka text-[10px] leading-snug text-[#6757B6] sm:text-xs">
                {characterFeedback.message}
              </p>
              <span className="absolute -bottom-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rotate-45 border-b-2 border-r-2 border-[#A594E9]/50 bg-white" />
            </div>
          )}

          <img
            src={selenaImage}
            alt="Selena mendampingi permainan"
            draggable={false}
            className="relative z-10 h-auto w-full object-contain drop-shadow-[0_12px_16px_rgba(83,73,151,0.2)] animate-game-char-float-delayed"
          />
        </div>
      </div>
      {/* CHARACTER FEEDBACK END */}

      <div className="h-[190px] shrink-0 bg-[#F5EBDD] border-t-2 border-[#DFCDB8] relative z-[50] shadow-[0_-12px_30px_rgba(73,55,45,0.11)] touch-none overflow-hidden">
        <div className="absolute inset-x-4 top-3 bottom-4 rounded-[1.65rem] border-2 border-white/90 bg-white/60 shadow-[inset_0_3px_10px_rgba(115,86,63,0.08)]" />
        <div className="absolute inset-x-8 top-[14px] h-7 rounded-full bg-[#E8D5BF]/65 border border-white/80 shadow-sm flex items-center justify-center">
          <span className="text-[#8D6E63]/55 font-fredoka text-[9px] uppercase tracking-[0.48em] select-none">
            RAK KEPINGAN PUZZLE
          </span>
        </div>
        <div className="absolute inset-x-0 bottom-0 h-4 bg-[#DCC4A9] border-t border-[#CDAF8F] shadow-[0_-3px_8px_rgba(91,70,55,0.12)]" />
      </div>

      <div className="fixed inset-0 pointer-events-none z-[150] touch-none">
        {pieces.map((p) => {
          const path = generateJigsawPath(
            p.row,
            p.col,
            puzzle.rows,
            puzzle.cols,
            pieceWidth,
            pieceHeight
          );

          const isActive = activePieceId === p.id;
          const canDrag = isGameActive && !p.isLocked;

          return (
            <div
              key={p.id}
              onMouseDown={(e) => handleMouseDown(p.id, e)}
              onTouchStart={(e) => handleMouseDown(p.id, e)}
              className={`absolute pointer-events-auto transition-shadow ${
                canDrag ? 'cursor-grab active:cursor-grabbing' : ''
              }`}
              style={{
                left: p.currentPos.x,
                top: p.currentPos.y,
                width: pieceWidth + PIECE_PADDING,
                height: pieceHeight + PIECE_PADDING,
                zIndex: isActive ? 3000 : p.isLocked ? 1 : p.zIndex,
                transform: `translate(${-PIECE_PADDING / 2}px, ${-PIECE_PADDING / 2}px) ${
                  isActive
                    ? 'scale(1.08) rotate(0deg)'
                    : p.isLocked
                    ? 'scale(1) rotate(0deg)'
                    : `scale(1) rotate(${((p.row * 7 + p.col * 11) % 9) - 4}deg)`
                }`,
                transition: isActive
                  ? 'none'
                  : 'transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s',
                opacity: !isGameActive && !p.isLocked ? 0.3 : 1,
                touchAction: 'none',
              }}
            >
              <svg
                width={pieceWidth + PIECE_PADDING}
                height={pieceHeight + PIECE_PADDING}
                viewBox={`${-PIECE_PADDING / 2} ${-PIECE_PADDING / 2} ${pieceWidth + PIECE_PADDING} ${pieceHeight + PIECE_PADDING}`}
                className={
                  isActive
                    ? 'drop-shadow-[0_25px_30px_rgba(0,0,0,0.6)]'
                    : p.isLocked
                    ? 'drop-shadow-sm'
                    : 'drop-shadow-[0_6px_10px_rgba(0,0,0,0.35)]'
                }
              >
                <defs>
                  <clipPath id={`clip-${p.id}`}>
                    <path d={path} />
                  </clipPath>
                </defs>

                <path
                  d={path}
                  fill="white"
                  stroke={p.isLocked ? 'white' : '#1E2939'}
                  strokeWidth={p.isLocked ? 1.3 : 2.4}
                />

                <g clipPath={`url(#clip-${p.id})`}>
                  <image
                    href={puzzle.image}
                    width={boardWidth}
                    height={boardHeight}
                    x={-p.targetPos.x}
                    y={-p.targetPos.y}
                    preserveAspectRatio="none"
                  />
                </g>
              </svg>
            </div>
          );
        })}
      </div>

      {isStarting && (
        <div className="fixed inset-0 z-[1000] bg-[#1E2939]/60 backdrop-blur-md flex items-center justify-center p-6 animate-fade-in">
          <div className="relative flex items-end justify-center w-full max-w-2xl gap-4">
            {/* Solis kiri */}
            <div className="hidden sm:flex flex-col items-center shrink-0 pb-4 animate-pop" style={{ animationDelay: '0.15s' }}>
              <img
                src="/games/puzzle/dist/assets/karakter/solis-semangat.png"
                alt="Solis"
                draggable={false}
                className="w-28 md:w-36 object-contain drop-shadow-xl animate-game-char-float"
              />
            </div>

            {/* Card tengah */}
            <div className="bg-white p-8 md:p-12 rounded-[4rem] border-[12px] border-[#81C784] shadow-2xl text-center transform animate-pop flex-1 min-w-0">
              <div className="text-7xl md:text-8xl mb-4 md:mb-6 animate-bounce">🌱</div>
              <h2 className="text-4xl md:text-5xl font-fredoka text-[#1E2939] mb-3 uppercase">Siap Main?</h2>
              <p className="text-base md:text-xl text-[#81C784]/80 mb-8 md:mb-10 font-bold">Susun cepat untuk skor maksimal!</p>

              <button
                onClick={startCountdown}
                className="group w-full bg-[#81C784] hover:bg-[#639C62] text-white font-fredoka text-3xl md:text-4xl py-6 md:py-8 rounded-[2.5rem] shadow-[0_12px_0_0_#4A7B52] hover:shadow-[0_6px_0_0_#4A7B52] hover:translate-y-[6px] active:translate-y-[12px] active:shadow-none transition-all flex items-center justify-center gap-4"
              >
                MULAI!
                <span className="text-4xl md:text-5xl group-hover:rotate-12 transition-transform">🚀</span>
              </button>
            </div>

            {/* Selena kanan */}
            <div className="hidden sm:flex flex-col items-center shrink-0 pb-4 animate-pop" style={{ animationDelay: '0.25s' }}>
              <img
                src="/games/puzzle/dist/assets/karakter/selena-melambai.png"
                alt="Selena"
                draggable={false}
                className="w-28 md:w-36 object-contain drop-shadow-xl animate-game-char-float-delayed"
              />
            </div>
          </div>
        </div>
      )}

      {countdown !== null && (
        <div className="fixed inset-0 z-[1100] flex items-center justify-center pointer-events-none bg-black/10">
          <div
            key={countdown}
            className={`text-[15rem] md:text-[25rem] font-fredoka animate-countdown-pop drop-shadow-[0_20px_50px_rgba(0,0,0,0.3)]
              ${countdown === 3 ? 'text-[#FFF54F]' : ''}
              ${countdown === 2 ? 'text-[#FF7316]' : ''}
              ${countdown === 1 ? 'text-[#DC2626]' : ''}
              ${countdown === 'GO' ? 'text-[#81C784]' : ''}
            `}
          >
            {countdown}
          </div>
        </div>
      )}

      {wrongFeedback && (
        <div
          className="fixed z-[5000] pointer-events-none animate-wrong-pop"
          style={{
            left: wrongFeedback.x,
            top: wrongFeedback.y,
          }}
        >
          <div className="relative -translate-x-1/2 -translate-y-1/2 flex flex-col items-center">
            <div className="bg-[#DC2626] text-white font-fredoka text-7xl px-7 py-3 rounded-3xl border-[6px] border-white shadow-2xl rotate-12">
              X
            </div>
          </div>
        </div>
      )}

      {isTimeUp && !isFinished && (
        <div className="fixed inset-0 z-[2000] flex items-center justify-center bg-[#1E2939]/80 backdrop-blur-xl animate-fade-in p-6">
          <div className="relative flex items-end justify-center w-full max-w-2xl gap-4">
            {/* Solis kiri - ekspresi terkejut */}
            <div className="hidden sm:flex flex-col items-center shrink-0 pb-4 animate-pop" style={{ animationDelay: '0.1s' }}>
              <img
                src="/games/puzzle/dist/assets/ekspresi/solis-terkejut.png"
                alt="Solis terkejut"
                draggable={false}
                className="w-24 md:w-32 object-contain drop-shadow-xl animate-game-char-float"
              />
            </div>

            {/* Card tengah */}
            <div className="bg-white p-8 md:p-12 rounded-[4rem] text-center shadow-2xl border-[10px] border-[#DC2626] transform animate-pop flex-1 min-w-0">
              <div className="text-7xl md:text-8xl mb-4 md:mb-6">⏳</div>
              <h2 className="text-4xl md:text-5xl font-fredoka text-[#DC2626] mb-2 uppercase">Yah Habis!</h2>
              <p className="text-base md:text-xl text-slate-500 mb-8 md:mb-10 font-bold">Waktunya habis, coba lagi yuk!</p>
              <div className="flex flex-col gap-4">
                <button
                  onClick={resetGame}
                  className="bg-[#DC2626] hover:bg-[#B71C1E] text-white font-fredoka text-2xl md:text-3xl py-5 md:py-6 w-full rounded-full shadow-[0_10px_0_0_#7A0F0F] active:translate-y-2 active:shadow-none transition-all"
                >
                  ULANGI ♻️
                </button>
                <button
                  onClick={onExit}
                  className="bg-slate-200 hover:bg-slate-300 text-slate-700 font-fredoka text-xl md:text-2xl py-4 w-full rounded-full shadow-[0_6px_0_0_#94a3b8] active:translate-y-1 active:shadow-none transition-all"
                >
                  KEMBALI ←
                </button>
              </div>
            </div>

            {/* Selena kanan - ekspresi peduli */}
            <div className="hidden sm:flex flex-col items-center shrink-0 pb-4 animate-pop" style={{ animationDelay: '0.2s' }}>
              <img
                src="/games/puzzle/dist/assets/ekspresi/selena-peduli.png"
                alt="Selena peduli"
                draggable={false}
                className="w-24 md:w-32 object-contain drop-shadow-xl animate-game-char-float-delayed"
              />
            </div>
          </div>
        </div>
      )}

      {isFinished && <SuccessModal score={score} onNext={onWin} />}

      <style>{`
        @keyframes pulse-slow {
          0%, 100% {
            transform: scale(1);
            opacity: 1;
          }
          50% {
            transform: scale(1.02);
            opacity: 0.9;
          }
        }

        .animate-pulse-slow {
          animation: pulse-slow 3s ease-in-out infinite;
        }

        @keyframes countdown-pop {
          0% {
            transform: scale(0) rotate(-20deg);
            opacity: 0;
          }
          40% {
            transform: scale(1.2) rotate(10deg);
            opacity: 1;
          }
          100% {
            transform: scale(1) rotate(0deg);
            opacity: 0;
          }
        }

        .animate-countdown-pop {
          animation: countdown-pop 1s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        .animate-fade-in {
          animation: fade-in 0.5s ease-out forwards;
        }

        .animate-pop {
          animation: pop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes fade-in {
          from {
            opacity: 0;
          }
          to {
            opacity: 1;
          }
        }

        @keyframes pop {
          from {
            transform: scale(0.5);
            opacity: 0;
          }
          to {
            transform: scale(1);
            opacity: 1;
          }
        }

        @keyframes wrong-pop {
          0% {
            transform: scale(0);
            opacity: 0;
          }
          50% {
            transform: scale(1.2);
            opacity: 1;
          }
          100% {
            transform: scale(1);
            opacity: 0;
          }
        }

        .animate-wrong-pop {
          animation: wrong-pop 0.8s ease-out forwards;
        }

        /* CHARACTER FEEDBACK START */
        @keyframes game-char-float {
          0%, 100% {
            transform: translateY(0);
          }

          50% {
            transform: translateY(-8px);
          }
        }

        @keyframes game-char-float-delayed {
          0%, 100% {
            transform: translateY(-3px);
          }

          50% {
            transform: translateY(5px);
          }
        }

        .animate-game-char-float {
          animation: game-char-float 5s ease-in-out infinite;
        }

        .animate-game-char-float-delayed {
          animation: game-char-float-delayed 5.4s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {
          .animate-game-char-float,
          .animate-game-char-float-delayed {
            animation: none !important;
          }
        }
        /* CHARACTER FEEDBACK END */
      `}</style>
    </div>
  );
};

export default GameBoard;
