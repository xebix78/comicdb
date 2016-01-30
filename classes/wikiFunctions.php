<?php
/**
 *
 * @author asanchez
 * <pre>
 *Functions:
 *seriesSearch
 *SeriesLookup
 *issueSearch
 *</pre>
 */
class wikiQuery {
	private $jsondata;
	private $results;
	private $detailResults;
	private $subject;
	private $pattern;
	private $replacement;
	private $fileparts;
	private $newfname;
	private $path;
	private $file;
	private $newf;
	private $url;
	/**
	 * a list of search results from ComicVine
	 * @var string
	 */
	public $resultsList;
	public $coverURL;
	public $coverFile;
	public $storyName;
	public $synopsis;
	public $coverSearchErr;
	public $apiURL;
	public $resultNum;
	public $seriesName;
	public $cvVolumeID;
	public $seriesStartYear;
	public $seriesURL;
	public $apiDetailURL;
	public $siteDetailURL;


	/**
	 * queries ComicVine API to get the API URL of the series searched for
	 * @param  int $seriesName name of the comic series being searched for
	 * @return string             list of results
	 */
	public function seriesSearch ($seriesName) {
		$apiURL = "http://www.comicvine.com/api/volumes/?api_key=8c685f7695c1dda5a4ecdf35c54402438a77b691&format=json&filter=name:$seriesName";
		$jsondata = file_get_contents($apiURL);
		$results = json_decode($jsondata, true);
		$this->resultNum = 1;
			foreach($results['results'] as $result) {
				$this->seriesName = $result['name'];
				$this->cvVolumeID = $result['id'];
				$this->seriesStartYear = $result['start_year'];
				$this->seriesURL = $result['site_detail_url'];
				$this->apiURL = $result['api_detail_url'];
				$this->resultsList .= '<div class="series-search-result col-xs-12 col-sm-6 col-md-4">
											<input name="apiURL" id="apiURL-' . $this->cvVolumeID . '" value="' . $this->apiURL . '" type="radio" />
												<label for="apiURL-' . $this->cvVolumeID . '">' . $this->resultNum . ': '  . $this->seriesName . ' ' . $this->seriesStartYear .'</label>
											<a href="' . $this->seriesURL . '" target="_blank">' . $this->seriesURL . '</a>
										</div>';
				++$this->resultNum;
			}
	}

	/**
	 * Uses a comic series' API URL to get information about that series.
	 * @param  string $apiDetailURL The API URL for a comic series
	 * @return string               gets series name, volume ID, series start year, and details URLs
	 */
	public function seriesLookup ($apiDetailURL) {
		$apiURL = $apiDetailURL . "?api_key=8c685f7695c1dda5a4ecdf35c54402438a77b691&format=json";
		$jsondata = file_get_contents($apiURL);
		$results = json_decode($jsondata, true);
		$this->seriesName = $results['results']['name'];
		$this->cvVolumeID = $results['results']['id'];
		$this->seriesStartYear = $results['results']['start_year'];
		$this->siteDetailURL = $results['results']['site_detail_url'];
		$this->apiDetailURL = $results['results']['api_detail_url'];
	}

	/**
	 * Uses a series' ComicVine volume ID and a comic's issue number to get issue details
	 * @param  int $cvVolumeID   ComicVine volume ID of the series
	 * @param  int $issue_number issue number of the comic
	 * @return string               issue's story name, plot, etc.
	 */
	public function issueSearch ($cvVolumeID, $issue_number) {
		$apiURL = "http://www.comicvine.com/api/issues/?filter=volume:$cvVolumeID,issue_number:$issue_number&format=json&api_key=8c685f7695c1dda5a4ecdf35c54402438a77b691";
		$jsondata = file_get_contents($apiURL);
		$results = json_decode($jsondata, true);
		$apiDetailURL = $results['results']['0']['api_detail_url'] . "?format=json&api_key=8c685f7695c1dda5a4ecdf35c54402438a77b691";
		$jsondata = file_get_contents($apiDetailURL);
		$detailResults = json_decode($jsondata, true);
		$this->storyName = $detailResults['results']['name'];
		$this->releaseDate = $detailResults['results']['cover_date'];
		$this->synopsis = $detailResults['results']['description'];
		$this->seriesName = $detailResults['results']['volume']['name'];
		$issueCreditsArray = $detailResults['results']['person_credits'];
		$this->issueCreditsArray = $issueCreditsArray;
		$pencils = '';
		$script = '';
		$colors = '';
		$cover = '';
		$editing = '';
		$letters = '';

		if (count($issueCreditsArray) > 0) {
			foreach($issueCreditsArray as $item) {
				if ($item['role'] == 'artist' || $item['role'] == 'artist, other' || $item['role'] == 'penciler' || $item['role'] == 'penciler, other' || $item['role'] == 'writer, penciler, inker, cover' || $item['role'] == 'penciler, cover' || $item['role'] == 'artist, penciler, cover') {
					$pencils .= '<span>' . $item['name'] . '</span>';
				}
				if ($item['role'] == 'writer' || $item['role'] == 'writer, other') {
					$script .= '<span>' . $item['name'] . '</span>';
				}
				if ($item['role'] == 'colorist' || $item['role'] == 'colorist, other' || $item['role'] == 'inker' || $item['role'] == 'inker, other' || $item['role'] == 'writer, penciler, inker, cover') {
					$colors .= '<span>' . $item['name'] . '</span>';
				}
				if ($item['role'] == 'editor' || $item['role'] == 'editor, other') {
					$editing .= '<span>' . $item['name'] . '</span>';
				}
				if ($item['role'] == 'cover' || $item['role'] == 'writer, penciler, inker, cover' || $item['role'] == 'penciler, cover' || $item['role'] == 'artist, penciler, cover') {
					$cover .= '<span>' . $item['name'] . '</span>';
				}
				if ($item['role'] == 'letterer') {
					$letters .= '<span>' . $item['name'] . '</span>';
				}
			}
		}

		$this->pencils = $pencils;
		$this->script = $script;
		$this->colors = $colors;
		$this->editing = $editing;
		$this->cover = $cover;
		$this->letters = $letters;

		if ($detailResults['results']['image']['medium_url']) {
			$subject = $detailResults['results']['image']['medium_url'];
			$pattern = "/(?<=jpg|png|jpeg).*/";
			$replacement = "";
			$this->coverURL = preg_replace($pattern, $replacement, $subject);
			$fileparts = explode("/", $this->coverURL);
			$this->coverFile = 'images/' . $fileparts[7];
			$this->coverFile = str_replace("%28", "", $this->coverFile);
			$this->coverFile = str_replace("%29", "", $this->coverFile);
			$this->coverFile = str_replace("%3F", "", $this->coverFile);
		} else {
			$this->coverFile = 'assets/nocover.jpg';
			$this->coverURL = 'assets/nocover.jpg';
			$this->noCover = true;
		}
	}

	public function downloadFile($url, $path) {
		$newfname = $path;
		$file = fopen ( $url, "rb" );
		if ($file) {
			$newf = fopen ( $newfname, "wb" );

			if ($newf) {
				while ( ! feof ( $file ) ) {
					fwrite ( $newf, fread ( $file, 1024 * 8 ), 1024 * 8 );
				}
			} else {
				die ( 'Could not write cover image file.' );
			}
		}

		if ($file) {
			fclose ( $file );
		}

		if ($newf) {
			fclose ( $newf );
		}
	}
}