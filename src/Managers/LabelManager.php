<?php

namespace HeroQR\Managers;

use Endroid\QrCode\Label\Margin\MarginInterface;
use Endroid\QrCode\Label\Font\FontInterface;
use HeroQR\Contracts\Managers\LabelManagerInterface;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\Margin\Margin;
use InvalidArgumentException;

/**
 * Class LabelManager
 * Manages label settings such as font, color, text, margin, and alignment for the QR code label.
 */
class LabelManager implements LabelManagerInterface
{
    private string $label = '';

    /**
     * LabelManager constructor.
     * 
     * @param ColorManager $labelColor The color manager instance to handle label colors.
     * @param MarginInterface $labelMargin The margin settings for the label (default: [0, 10, 10, 10]).
     * @param FontInterface $labelFont The font for the label text (default: OpenSans with size 20).
     * @param LabelAlignment $labelAlign The alignment for the label (default: center).
     */
    public function __construct(
        private ColorManager $labelColor,
        private MarginInterface $labelMargin = new Margin(0, 10, 10, 10),
        private FontInterface $labelFont = new OpenSans(20),
        private LabelAlignment $labelAlign = LabelAlignment::Center
    ) {}

    /**
     * Set the label text.
     * 
     * @param string $label The text to display on the label.
     * @throws InvalidArgumentException If the label text is empty or too long.
     */
    public function setLabel(string $label): void
    {
        if (empty($label)) {
            throw new InvalidArgumentException('Label Text Cannot Be Empty');
        }

        if (strlen($label) > 200) {
            throw new InvalidArgumentException('Label Text Cannot Exceed 200 Characters');
        }

        $this->label = htmlspecialchars($label);
    }

    /**
     * Get the current label text.
     * 
     * @return string The label text.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the font used for the label.
     * 
     * @return FontInterface The font used for the label text.
     */
    public function getLabelFont(): FontInterface
    {
        return $this->labelFont;
    }

    /**
     * Set the font size for the label.
     * 
     * @param int $size The font size to apply.
     * @throws InvalidArgumentException If the size is not a positive integer.
     */
    public function setLabelSize(int $size): void
    {
        if ($size <= 0) {
            throw new InvalidArgumentException('Font Size Must Be A Positive Integer');
        }

        $this->labelFont = new OpenSans($size);
    }

    /**
     * Set the alignment for the label.
     * 
     * @param string $labelAlign The alignment (left, center, or right).
     * @throws InvalidArgumentException If an invalid alignment is provided.
     */
    public function setLabelAlign(string $labelAlign): void
    {
        $labelAlign = strtolower($labelAlign);
        if (!in_array($labelAlign, ['left', 'center', 'right'], true)) {
            throw new InvalidArgumentException('Invalid Label Alignment. Allowed Values Are "left", "center", or "right"');
        }

        $this->labelAlign = LabelAlignment::from($labelAlign);
    }

    /**
     * Get the current label alignment.
     * 
     * @return LabelAlignment The current label alignment.
     */
    public function getLabelAlign(): LabelAlignment
    {
        return $this->labelAlign;
    }

    /**
     * Set the label color.
     * 
     * @param string $color The color in hex format (e.g., "#FF5733").
     * @throws InvalidArgumentException If the color format is invalid.
     */
    public function setLabelColor(string $hexColor): void
    {
        if (!$this->isValidHexColor($hexColor)) {
            throw new InvalidArgumentException('Invalid Label Color Format');
        }

        $this->labelColor->setLabelColor($hexColor);
    }

    /**
     * Get the current label color.
     * 
     * @return ColorInterface The current label color.
     */
    public function getLabelColor(): ColorInterface
    {
        return $this->labelColor->getLabelColor();
    }

    /**
     * Set the label margin.
     * 
     * @param array $margin An array of margin values [top, right, bottom, left].
     * @throws InvalidArgumentException If the margin array does not contain exactly 4 values.
     */
    public function setLabelMargin(array $margin): void
    {
        if (count($margin) !== 4) {
            throw new InvalidArgumentException('Margin Array Must Contain Exactly 4 Values [top, right, bottom, left]');
        }

        foreach ($margin as $key => $value) {
            if (!is_numeric($value)) {
                throw new InvalidArgumentException("Margin Value At Index {$key} Must Be Numeric Value");
            }

            if ($value < -250 || $value > 250) {
                throw new InvalidArgumentException("Margin Value At Index {$key} Must Be Between -250 And 250");
            }
        }

        $this->labelMargin = new Margin($margin[0], $margin[1], $margin[2], $margin[3]);
    }


    /**
     * Get the current label margin.
     * 
     * @return MarginInterface The current label margin.
     */
    public function getLabelMargin(): MarginInterface
    {
        return $this->labelMargin;
    }

    /**
     * Helper method to validate if a hex color is valid.
     *
     * @param string $hexColor The color code to validate.
     * @return bool True if the color code is valid, false otherwise.
     */
    private function isValidHexColor(string $hexColor): bool
    {
        return preg_match('/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6}|[a-fA-F0-9]{8})$/', $hexColor) === 1;
    }
}
