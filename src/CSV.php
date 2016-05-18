<?php
namespace Dakov;

/**
 * Class CSV
 *
 * @package csv
 *
 * @author Stanislav Dakov st.dakov@gmail.com
 */
class CSV
{
    /**@var string $file */
    protected $file = 'export.csv';
    /** @var  string $path */
    protected $path;
    /**@var array $data */
    protected $data;
    /** @var array $headers */
    protected $headers = [];
    /** @var int $fileSize */
    protected $fileSize = 0;

    public function __construct($path = '')
    {
        if (!$path) {
            $path = getcwd();
        }

        $path = rtrim($path, DIRECTORY_SEPARATOR);

        if (!is_dir($path)) {
            throw new \Exception('Wrong or missing folder: ' . $path);
        }

        $this->path = $path;

        $this->file = $path . DIRECTORY_SEPARATOR . date("Y-m-d_H:i:s") . '_' . $this->file;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setFile($file)
    {
        if (file_exists($file)) {
            $this->file = $file;
        } else {
            $this->file = $this->path . DIRECTORY_SEPARATOR . $file;
        }

        return $this;
    }

    /**
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function create(array $data)
    {
        if (file_exists($this->file)) {
            $this->fileSize = filesize($this->file);

            if ($this->fileSize > 0) {
                throw new \Exception('The csv file "' . $this->file . '" is not empty. Use append()');
            }
        }

        $fh = fopen($this->file, 'w');

        $headers = false;

        foreach ($data as $value) {
            if ($this->fileSize == 0 && !$headers) {
                $this->headers = array_keys($value);
                fputcsv($fh, $this->headers);
                $headers = true;
            }
            fputcsv($fh, $value);
        }

        fclose($fh);

        return $this->file;
    }

    /**
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function append(array $data)
    {
        if (!file_exists($this->file)) {
            throw new \Exception('The csv file "' . $this->file . '" is missing. Use create() first');
        }

        $fh = fopen($this->file, 'a');

        foreach ($data as $value) {
            fputcsv($fh, $value);
        }

        fclose($fh);

        return $this->file;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function read()
    {
        if (!file_exists($this->file)) {
            throw new \Exception('The csv file "' . $this->file . '" is missing.');
        }

        $this->fileSize = filesize($this->file);

        if ($this->fileSize == 0) {
            return [];
        }

        $csvData = [];
        $fields = [];
        $i = 0;
        $fh = fopen($this->file, "r");

        while (($row = fgetcsv($fh)) !== false) {
            if (empty($fields)) {
                $fields = $row;
                continue;
            }

            foreach ($row as $k => $value) {
                $csvData[$i][$fields[$k]] = $value;
            }
            $i++;
        }

        fclose($fh);

        return $csvData;
    }

    /**
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function readReverse($limit = 0)
    {
        if (!file_exists($this->file)) {
            throw new \Exception('The csv file "' . $this->file . '" is missing.');
        }

        $this->fileSize = filesize($this->file);

        if ($this->fileSize == 0) {
            return [];
        }

        $fh = fopen($this->file, 'r');

        $pos = -1;

        $data = [];
        $currentLine = '';

        $header = str_replace(PHP_EOL, '', fgets($fh));
        $header = explode(',', $header);

        while (!fseek($fh, $pos, SEEK_END)) {
            if ($limit > 0 && $limit == count($data)) {
                break;
            }

            $pos--;
            $char = fgetc($fh);

            //put collected line in array
            if ($char == PHP_EOL) {
                if ($currentLine != '') {
                    $lineValues = explode(',', $currentLine);
                    $lineData = [];

                    foreach ($header as $key => $column) {
                        $lineData[$column] = $lineValues[$key];
                    }

                    $data[] = $lineData;
                    $currentLine = '';
                }
                continue;
            }

            //collect until new line
            $currentLine = $char . $currentLine;
        }

        fclose($fh);

        return $data;
    }

    /**
     * @param bool|false $cleanFile
     */
    public function download($cleanFile = false)
    {
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header('Content-Disposition: attachment; filename="' . basename($this->file) . '"');
        header("Expires: 0");
        header("Pragma: public");

        readfile($this->file);

        if ($cleanFile) {
            unlink($this->file);
        }

        exit();
    }
}
