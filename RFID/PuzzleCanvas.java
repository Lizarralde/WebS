import java.io.IOException;
import java.util.Random;

import javax.microedition.lcdui.Graphics;
import javax.microedition.lcdui.Image;
import javax.microedition.lcdui.game.GameCanvas;

public class PuzzleCanvas extends GameCanvas implements Runnable {

	private static final int SLEEP_INTERVAL = 1000;
	private Thread puzzleThread;
	private boolean easyMode;
	private boolean puzzleGameOver;
	private boolean puzzleThreadQuit;
	private Image[] puzzleImages;
	private int[][] puzzleStatus;
	private int nbCoups;
	private long startTime;
	private int screenWidth = this.getWidth();
	private int screenHeight = this.getHeight();

	protected PuzzleCanvas(boolean suppressKeyEvents, boolean easyMode)
			throws InterruptedException, IOException {

		super(suppressKeyEvents);

		this.easyMode = easyMode;

		this.puzzleImages = new Image[9];
		this.puzzleStatus = new int[3][3];

		for (int i = 0; i < 9; i++) {

			this.puzzleImages[i] = resizeImage(Image.createImage("img/TUX/TUX-"
					+ (i + 1) + ".png"));
		}

		newGame();
	}

	public void newGame() throws InterruptedException {

		if (this.puzzleThread != null) {

			stopGame();
		}

		this.puzzleThread = new Thread(this);

		this.puzzleGameOver = false;

		this.puzzleThreadQuit = false;

		this.puzzleThread.start();
	}

	public void stopGame() throws InterruptedException {

		this.puzzleThreadQuit = true;

		this.puzzleThread.join();

		this.puzzleThread = null;
	}

	public void run() {

		generateRandomPuzzleStatus();

		this.startTime = System.currentTimeMillis();

		while (!this.puzzleThreadQuit) {

			updateGameScreen();

			verifyGameState();

			try {

				this.puzzleThread.sleep(PuzzleCanvas.SLEEP_INTERVAL);
			} catch (InterruptedException e) {

				e.printStackTrace();
			}
		}
	}

	private void generateRandomPuzzleStatus() {

		int[] values = new int[] { 0, 1, 2, 3, 4, 5, 6, 7 };

		shuffleArray(values);

		for (int i = 0; i < 8; i++) {

			this.puzzleStatus[i / 3][i % 3] = values[i];
		}

		this.puzzleStatus[2][2] = this.puzzleStatus[2][0];

		this.puzzleStatus[2][0] = -1;

		if (easyMode) {

			this.puzzleStatus[0][0] = 0;
			this.puzzleStatus[0][1] = 1;
			this.puzzleStatus[0][2] = 2;
			this.puzzleStatus[1][0] = 3;
			this.puzzleStatus[1][1] = 4;
			this.puzzleStatus[1][2] = 5;
			this.puzzleStatus[2][0] = -1;
			this.puzzleStatus[2][1] = 6;
			this.puzzleStatus[2][2] = 7;
		}
	}

	private void updateGameScreen() {

		this.repaint();
	}

	private void verifyGameState() {

		for (int i = 0; i < 8; i++) {

			if (this.puzzleStatus[i / 3][i % 3] != i) {

				return;
			}
		}

		puzzleGameOver = true;
	}

	public void paint(Graphics g) {

		super.paint(g);

		int width = this.getWidth();
		int height = this.getHeight();

		int fontHeight = g.getFont().getHeight();

		long time;

		g.setColor(0);
		g.fillRect(0, 0, screenWidth, screenHeight);

		int index;
		Image img;

		for (int i = 0; i < 9; i++) {

			index = this.puzzleStatus[i / 3][i % 3];

			if (index != -1) {

				img = this.puzzleImages[index];

				g.drawImage(img, (i % 3) * (screenWidth / 3), (i / 3)
						* (screenHeight / 3), Graphics.TOP | Graphics.LEFT);
			}
		}

		if (puzzleGameOver) {

			img = this.puzzleImages[8];

			time = (System.currentTimeMillis() - this.startTime) / 60000;

			g.drawImage(img, 2 * (screenWidth / 3), 2 * (screenHeight / 3),
					Graphics.TOP | Graphics.LEFT);

			g.fillRect(0, height / 2 - fontHeight, width, fontHeight);

			g.fillRect(0, height - fontHeight, width, fontHeight);

			g.setColor(0xFFFFFF);

			g.drawString("GAME OVER", width / 2, height / 2, Graphics.BOTTOM
					| Graphics.HCENTER);

			g.drawString("Temps : " + time + " min, Permutation : "
					+ this.nbCoups, width / 2, height, Graphics.BOTTOM
					| Graphics.HCENTER);
		}
	}

	/**
	 * This methog resizes an image by resampling its pixels
	 * 
	 * @param src
	 *            The image to be resized
	 * @return The resized image
	 */
	private Image resizeImage(Image src) {
		int srcWidth = src.getWidth();
		int srcHeight = src.getHeight();
		Image tmp = Image.createImage((screenWidth / 3), srcHeight);
		Graphics g = tmp.getGraphics();
		int ratio = (srcWidth << 16) / (screenWidth / 3);
		int pos = ratio / 2;

		// Horizontal Resize

		for (int x = 0; x < (screenWidth / 3); x++) {
			g.setClip(x, 0, 1, srcHeight);
			g.drawImage(src, x - (pos >> 16), 0, Graphics.LEFT | Graphics.TOP);
			pos += ratio;
		}

		Image resizedImage = Image.createImage((screenWidth / 3),
				(screenHeight / 3));
		g = resizedImage.getGraphics();
		ratio = (srcHeight << 16) / (screenHeight / 3);
		pos = ratio / 2;

		// Vertical resize

		for (int y = 0; y < (screenHeight / 3); y++) {
			g.setClip(0, y, (screenWidth / 3), 1);
			g.drawImage(tmp, 0, y - (pos >> 16), Graphics.LEFT | Graphics.TOP);
			pos += ratio;
		}
		return resizedImage;

	}// resize image

	private void shuffleArray(int[] ar) {

		Random rnd = new Random();

		for (int i = ar.length - 1; i > 0; i--) {

			int index = rnd.nextInt(i + 1);

			// Simple swap
			int a = ar[index];
			ar[index] = ar[i];
			ar[i] = a;
		}
	}

	protected void keyPressed(int keyCode) {

		super.keyPressed(keyCode);

		int index;
		int j = -1;

		for (int i = 0; i < 9; i++) {

			index = this.puzzleStatus[i / 3][i % 3];

			if (index == -1) {

				switch (keyCode) {

				case -1:
					j = i + 3;
					break;

				case -2:
					j = i - 3;
					break;

				case -3:
					j = i + 1;
					break;

				case -4:
					j = i - 1;
					break;
				}

				if (j > -1 && j < 9 && !puzzleGameOver) {

					this.puzzleStatus[i / 3][i % 3] = this.puzzleStatus[j / 3][j % 3];

					this.puzzleStatus[j / 3][j % 3] = -1;

					this.nbCoups++;
				}

				break;
			}
		}
	}
}
